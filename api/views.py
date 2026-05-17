import os
import uuid
import json
from django.http import JsonResponse, HttpResponse
from django.views.decorators.csrf import csrf_exempt
from dashboard.models import BinUser, Bin, PollutionLog, Reward, ClaimedReward
from django.apps import apps
from django.db.models import Sum
from django.utils import timezone

@csrf_exempt
def register(request):
    if request.method == 'POST':
        username = request.POST.get('username')
        password = request.POST.get('password')
        image = request.FILES.get('image')
        
        if not username or not image:
            return JsonResponse({'status': 'error', 'message': 'Username and image are required'}, status=400)
        
        if BinUser.objects.filter(username=username).exists():
            return JsonResponse({'status': 'error', 'message': 'Username already exists'}, status=400)
        
        # Save image for Face ID
        os.makedirs('faces_db', exist_ok=True)
        file_path = os.path.join('faces_db', f"{username}.jpg")
        with open(file_path, 'wb+') as destination:
            for chunk in image.chunks():
                destination.write(chunk)
        
        # Create user
        user = BinUser.objects.create(username=username, password=password, profile_picture=image)
        
        return JsonResponse({
            'status': 'success',
            'message': 'User registered successfully',
            'user': {
                'username': user.username, 
                'points': user.total_points,
                'profile_picture': request.build_absolute_uri(user.profile_picture.url) if user.profile_picture else None
            }
        })
    return JsonResponse({'status': 'error', 'message': 'Only POST allowed'}, status=405)

@csrf_exempt
def login(request):
    if request.method == 'POST':
        username = request.POST.get('username')
        password = request.POST.get('password')
        image = request.FILES.get('image')
        
        # 1. Traditional Login
        if username and password:
            user = BinUser.objects.filter(username=username, password=password).first()
            if user:
                return JsonResponse({
                    'status': 'success',
                    'user': {
                        'username': user.username, 
                        'points': user.total_points,
                        'profile_picture': request.build_absolute_uri(user.profile_picture.url) if user.profile_picture else None
                    }
                })
        
        # 2. Face Login
        if image:
            temp_path = f"temp_login_{uuid.uuid4()}.jpg"
            with open(temp_path, 'wb+') as destination:
                for chunk in image.chunks():
                    destination.write(chunk)
            
            try:
                ai = apps.get_app_config('api').ai_handler
                if not ai:
                    from ai_handler import AIHandler
                    ai = AIHandler()
                    apps.get_app_config('api').ai_handler = ai

                # Force refresh DeepFace cache if needed (optional, but safer for dev)
                cache_path = os.path.join("faces_db", "representations_vgg_face.pkl")
                if os.path.exists(cache_path):
                    try:
                        os.remove(cache_path)
                    except:
                        pass

                identity_path = ai.identify_face(temp_path, "faces_db/")
                if identity_path:
                    username_from_face = os.path.basename(identity_path).split('.')[0]
                    user = BinUser.objects.filter(username=username_from_face).first()
                    if user:
                         return JsonResponse({
                            'status': 'success',
                            'user': {
                                'username': user.username, 
                                'points': user.total_points,
                                'profile_picture': request.build_absolute_uri(user.profile_picture.url) if user.profile_picture else None
                            }
                        })
                return JsonResponse({'status': 'error', 'message': 'Face not recognized'}, status=401)
            finally:
                if os.path.exists(temp_path):
                    os.remove(temp_path)
                    
        return JsonResponse({'status': 'error', 'message': 'Invalid credentials'}, status=401)
    return JsonResponse({'status': 'error', 'message': 'Only POST allowed'}, status=405)

@csrf_exempt
def identify_face_esp(request):
    if request.method == 'POST':
        bin_id = request.GET.get('bin_id', 1) # Default to 1 for prototype
        image = request.body # ESP sends raw body
        
        if not image:
            return JsonResponse({'status': 'error', 'message': 'Image required'}, status=400)
            
        temp_path = f"temp_face_{uuid.uuid4()}.jpg"
        with open(temp_path, 'wb+') as destination:
            destination.write(image)
        
        try:
            ai = apps.get_app_config('api').ai_handler
            if not ai:
                from ai_handler import AIHandler
                ai = AIHandler()
                apps.get_app_config('api').ai_handler = ai
                
            identity_path = ai.identify_face(temp_path, "faces_db/")
            if identity_path:
                username = os.path.basename(identity_path).split('.')[0]
                user = BinUser.objects.filter(username=username).first()
                if user:
                    bin_obj = Bin.objects.filter(id=bin_id).first()
                    if bin_obj:
                        bin_obj.active_user = user
                        bin_obj.last_session_time = timezone.now()
                        bin_obj.save()
                    return HttpResponse("SUCCESS:" + username)
            return HttpResponse("FAILED:Not Recognized")
        finally:
            if os.path.exists(temp_path):
                os.remove(temp_path)
    return HttpResponse("ERROR:POST required")

@csrf_exempt
def process_trash_esp(request):
    if request.method == 'POST':
        bin_id = request.GET.get('bin_id', 1)
        image = request.body
        
        if not image:
            return JsonResponse({'status': 'error', 'message': 'Image required'}, status=400)
            
        temp_path = f"temp_trash_{uuid.uuid4()}.jpg"
        with open(temp_path, 'wb+') as destination:
            destination.write(image)
        
        try:
            ai = apps.get_app_config('api').ai_handler
            if not ai:
                from ai_handler import AIHandler
                ai = AIHandler()
                apps.get_app_config('api').ai_handler = ai
                
            # 1. Detect Trash Type
            waste_type = ai.detect_waste(temp_path) # Returns label from modelklasifikasi.pt
            category = "NON ORGANIK"
            
            # Fleksibilitas pencocokan untuk model custom (mengandung kata organik/food)
            if waste_type and any(word in waste_type.lower() for word in ["organic", "organik", "food", "sisa"]):
                category = "ORGANIK"
            
            # 2. Assign Points to Active User
            bin_obj = Bin.objects.filter(id=bin_id).first()
            user_msg = "GUEST"
            if bin_obj:
                bin_obj.last_scan_result = category
                if bin_obj.active_user:
                    # Check if session is still valid (e.g., < 2 minutes)
                    if bin_obj.last_session_time and (timezone.now() - bin_obj.last_session_time).total_seconds() < 120:
                        user = bin_obj.active_user
                        user.total_points += 10
                        user.save()
                        user_msg = user.username
                    # Clear session after use
                    bin_obj.active_user = None
                bin_obj.save()
                
            return HttpResponse(category)
        finally:
            if os.path.exists(temp_path):
                os.remove(temp_path)
    return HttpResponse("ERROR:POST required")

@csrf_exempt
def check_result(request):
    if request.method == 'GET':
        bin_id = request.GET.get('bin_id', 1)
        bin_obj = Bin.objects.filter(id=bin_id).first()
        if bin_obj and bin_obj.last_scan_result:
            result = bin_obj.last_scan_result
            bin_obj.last_scan_result = None # Clear after read
            bin_obj.save()
            return JsonResponse({'status': 'success', 'result': result})
        return JsonResponse({'status': 'waiting'})
    return JsonResponse({'status': 'error'}, status=405)

@csrf_exempt
def telemetry(request):
    if request.method == 'POST':
        try:
            data = json.loads(request.body)
            bin_id = data.get('bin_id')
            organic_level = data.get('organic_level')
            inorganic_level = data.get('inorganic_level')
            mq135_value = data.get('mq135_value')
            
            bin_entry = Bin.objects.filter(id=bin_id).first()
            if bin_entry:
                bin_entry.organic_level = organic_level
                bin_entry.inorganic_level = inorganic_level
                bin_entry.save()
                
                PollutionLog.objects.create(bin=bin_entry, mq135_value=mq135_value)
                
                return JsonResponse({'status': 'success', 'message': 'Telemetry received'})
            return JsonResponse({'status': 'error', 'message': 'Bin not found'}, status=404)
        except Exception as e:
            return JsonResponse({'status': 'error', 'message': str(e)}, status=400)
    return JsonResponse({'status': 'error', 'message': 'Only POST allowed'}, status=405)

@csrf_exempt
def trigger_session(request):
    if request.method == 'POST':
        bin_id = request.POST.get('bin_id', request.GET.get('bin_id', 1))
        username = request.POST.get('username')
        
        bin_obj = Bin.objects.filter(id=bin_id).first()
        if bin_obj:
            if username:
                user = BinUser.objects.filter(username=username).first()
                if user:
                    bin_obj.active_user = user
                    bin_obj.last_session_time = timezone.now()
            
            bin_obj.trigger_session = True
            bin_obj.save()
            return JsonResponse({'status': 'success', 'message': 'Session triggered'})
        return JsonResponse({'status': 'error', 'message': 'Bin not found'}, status=404)
    return JsonResponse({'status': 'error', 'message': 'Only POST allowed'}, status=405)

@csrf_exempt
def check_command(request):
    if request.method == 'GET':
        bin_id = request.GET.get('bin_id', 1)
        bin_obj = Bin.objects.filter(id=bin_id).first()
        if bin_obj:
            if bin_obj.trigger_session:
                bin_obj.trigger_session = False
                bin_obj.save()
                return HttpResponse("TRIGGER:1")
            return HttpResponse("TRIGGER:0")
        return HttpResponse("ERROR:Bin not found", status=404)
    return HttpResponse("ERROR:Only GET allowed", status=405)

@csrf_exempt
def get_rewards(request):
    rewards = Reward.objects.all()
    data = []
    for r in rewards:
        data.append({
            'id': r.id,
            'name': r.nama_reward,
            'points': r.syarat_point,
            'kuota': r.kuota,
            'description': r.keterangan,
            'status': r.status,
            'category': r.kategori,
            'image': request.build_absolute_uri(r.foto_reward.url) if r.foto_reward else None
        })
    return JsonResponse({'status': 'success', 'rewards': data})

@csrf_exempt
def claim_reward(request):
    if request.method == 'POST':
        username = request.POST.get('username')
        reward_id = request.POST.get('reward_id')
        
        user = BinUser.objects.filter(username=username).first()
        reward = Reward.objects.filter(id=reward_id).first()
        
        if not user or not reward:
            return JsonResponse({'status': 'error', 'message': 'User or Reward not found'}, status=404)
            
        if user.total_points < reward.syarat_point:
            return JsonResponse({'status': 'error', 'message': 'Insufficient points'}, status=400)
            
        if reward.kuota <= 0:
            return JsonResponse({'status': 'error', 'message': 'Reward out of stock'}, status=400)
            
        user.total_points -= reward.syarat_point
        user.save()
        
        reward.kuota -= 1
        reward.save()
        
        # Log the claim
        ClaimedReward.objects.create(user=user, reward=reward)
        
        return JsonResponse({
            'status': 'success',
            'message': f'Reward {reward.nama_reward} claimed!',
            'new_points': user.total_points
        })
    return JsonResponse({'status': 'error', 'message': 'Only POST allowed'}, status=405)

@csrf_exempt
def get_purchase_history(request):
    username = request.GET.get('username')
    user = BinUser.objects.filter(username=username).first()
    if not user:
        return JsonResponse({'status': 'error', 'message': 'User not found'}, status=404)
    
    history = ClaimedReward.objects.filter(user=user).order_by('-claimed_at')
    data = []
    for item in history:
        data.append({
            'id': item.id,
            'reward_name': item.reward.nama_reward,
            'points_spent': item.reward.syarat_point,
            'date': item.claimed_at.strftime('%Y-%m-%d %H:%M'),
            'image': request.build_absolute_uri(item.reward.foto_reward.url) if item.reward.foto_reward else None
        })
    return JsonResponse({'status': 'success', 'history': data})

@csrf_exempt
def update_profile(request):
    if request.method == 'POST':
        username = request.POST.get('username')
        new_username = request.POST.get('new_username')
        image = request.FILES.get('image')
        
        user = BinUser.objects.filter(username=username).first()
        if not user:
            return JsonResponse({'status': 'error', 'message': 'User not found'}, status=404)
        
        if new_username:
            # Check if new username exists
            if BinUser.objects.filter(username=new_username).exclude(id=user.id).exists():
                 return JsonResponse({'status': 'error', 'message': 'Username already taken'}, status=400)
            
            # Rename face photo if exists
            old_face_path = os.path.join('faces_db', f"{user.username}.jpg")
            new_face_path = os.path.join('faces_db', f"{new_username}.jpg")
            if os.path.exists(old_face_path):
                os.rename(old_face_path, new_face_path)
            
            user.username = new_username
            
        if image:
            user.profile_picture = image
            # Also update face db
            os.makedirs('faces_db', exist_ok=True)
            face_path = os.path.join('faces_db', f"{user.username}.jpg")
            with open(face_path, 'wb+') as destination:
                for chunk in image.chunks():
                    destination.write(chunk)
                    
        user.save()
        return JsonResponse({
            'status': 'success', 
            'user': {
                'username': user.username,
                'points': user.total_points,
                'profile_picture': request.build_absolute_uri(user.profile_picture.url) if user.profile_picture else None
            }
        })
    return JsonResponse({'status': 'error', 'message': 'Only POST allowed'}, status=405)

@csrf_exempt
def get_user_info(request):
    username = request.GET.get('username')
    user = BinUser.objects.filter(username=username).first()
    if not user:
        return JsonResponse({'status': 'error', 'message': 'User not found'}, status=404)
    
    return JsonResponse({
        'status': 'success',
        'user': {
            'username': user.username,
            'points': user.total_points,
            'profile_picture': request.build_absolute_uri(user.profile_picture.url) if user.profile_picture else None
        }
    })

@csrf_exempt
def get_bins(request):
    bins = Bin.objects.all()
    data = []
    for b in bins:
        latest_log = PollutionLog.objects.filter(bin=b).order_by('-timestamp').first()
        mq135_val = latest_log.mq135_value if latest_log else 0
        
        data.append({
            'id': b.id,
            'name': b.name,
            'lat': b.lat,
            'lng': b.long,
            'organic': b.organic_level,
            'inorganic': b.inorganic_level,
            'mq135': mq135_val,
            'last_update': b.last_update.strftime('%Y-%m-%d %H:%M')
        })
    return JsonResponse({'status': 'success', 'bins': data})

@csrf_exempt
def get_dashboard_stats(request):
    bins = Bin.objects.all()
    users_count = BinUser.objects.count()
    full_bins = Bin.objects.filter(organic_level__gte=80).count()
    
    total_organic = bins.aggregate(Sum('organic_level'))['organic_level__sum'] or 0
    total_inorganic = bins.aggregate(Sum('inorganic_level'))['inorganic_level__sum'] or 0
    total_waste = total_organic + total_inorganic
    
    from django.utils import timezone
    today = timezone.now().date()
    from dashboard.models import ClaimedReward
    reward_today = ClaimedReward.objects.filter(claimed_at__date=today).count()
    
    top_users = BinUser.objects.order_by('-total_points')[:10]
    top_users_data = []
    for u in top_users:
        top_users_data.append({
            'username': u.username,
            'points': u.total_points,
            'profile_picture': request.build_absolute_uri(u.profile_picture.url) if u.profile_picture else None
        })
    
    return JsonResponse({
        'status': 'success',
        'stats': {
            'total_users': users_count,
            'total_waste': int(total_waste),
            'active_bins': bins.count(),
            'full_bins': full_bins,
            'reward_today': reward_today,
            'top_users': top_users_data
        }
    })
