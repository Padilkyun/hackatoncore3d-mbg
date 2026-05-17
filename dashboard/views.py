from django.shortcuts import render, redirect, get_object_or_404
from .models import BinUser, Bin, PollutionLog, Reward
from django.db.models import Count, Sum
import json

from django.utils import timezone
from .models import BinUser, Bin, PollutionLog, Reward, ClaimedReward

def dashboard(request):
    period = request.GET.get('filter', 'today')
    
    bins = Bin.objects.all()
    users_count = BinUser.objects.count()
    full_bins = Bin.objects.filter(organic_level__gte=80).count()
    
    # Calculate real waste totals
    total_organic = bins.aggregate(Sum('organic_level'))['organic_level__sum'] or 0
    total_inorganic = bins.aggregate(Sum('inorganic_level'))['inorganic_level__sum'] or 0
    total_waste = total_organic + total_inorganic
    
    # Calculate percentages for the donut chart
    if total_waste > 0:
        org_p = int((total_organic / total_waste) * 100)
        inorg_p = 100 - org_p
    else:
        org_p, inorg_p = 50, 50
        
    today = timezone.now().date()
    reward_today = ClaimedReward.objects.filter(claimed_at__date=today).count()
    
    # Simple logic for filter display
    if period == 'monthly':
        total_waste *= 30
    elif period == 'yearly':
        total_waste *= 365
        
    stats = {
        'total_users': users_count,
        'total_waste': int(total_waste),
        'total_organic': int(total_organic),
        'total_inorganic': int(total_inorganic),
        'organic_percentage': org_p,
        'inorganic_percentage': inorg_p,
        'active_bins': bins.count(),
        'full_bins': full_bins,
        'reward_today': reward_today,
        'period': period.capitalize()
    }
    
    recent_bins = bins.order_by('-organic_level')[:5] 
    top_users = BinUser.objects.order_by('-total_points')[:10]
    
    # Create real-ish chart data based on current levels
    chart_days = ["Mon", "Tues", "Wed", "Thurs", "Fri", "Sat", "Sun"]
    import random
    chart_data = []
    avg_org = total_organic / 7 if total_organic > 0 else 50
    avg_inorg = total_inorganic / 7 if total_inorganic > 0 else 40
    
    for day in chart_days:
        # Add some randomness to the "History" for visualization
        chart_data.append({
            'day': day,
            'org': int(avg_org * (0.5 + random.random())),
            'inorg': int(avg_inorg * (0.5 + random.random()))
        })
    
    context = {
        'stats': stats,
        'recent_bins': recent_bins,
        'top_users': top_users,
        'chart_data': chart_data,
    }
    return render(request, 'dashboard/dashboard.html', context)

def bin_monitoring(request):
    bins = Bin.objects.all()
    # Format for template
    bins_data = []
    for b in bins:
        bins_data.append({
            'id': b.id,
            'name': b.name,
            'lat': b.lat,
            'long': b.long,
            'organic': b.organic_level,
            'inorganic': b.inorganic_level,
        })
    
    context = {
        'bins': bins_data,
    }
    return render(request, 'dashboard/bin_monitoring.html', context)

import math

def calculate_distance(lat1, lon1, lat2, lon2):
    R = 6371 # Earth radius in km
    dlat = math.radians(lat2 - lat1)
    dlon = math.radians(lon2 - lon1)
    a = math.sin(dlat / 2) * math.sin(dlat / 2) + \
        math.cos(math.radians(lat1)) * math.cos(math.radians(lat2)) * \
        math.sin(dlon / 2) * math.sin(dlon / 2)
    c = 2 * math.atan2(math.sqrt(a), math.sqrt(1 - a))
    return R * c

def route_map(request):
    # Base station (Padang City Center or similar)
    base_lat = -0.9471
    base_lon = 100.3695
    
    # Get all bins
    all_bins = Bin.objects.all()
    
    # Filter critical bins (Full now OR predicted to be full soon)
    # In a real app, you'd use LSTM here. For demo, we prioritize high organic_level.
    critical_bins = []
    for b in all_bins:
        # Mock LSTM logic: if organic > 70%, it's "predicted" to be full soon
        is_critical = b.organic_level >= 70
        if is_critical:
            critical_bins.append(b)
    
    # Sort bins using Nearest Neighbor starting from base station
    optimized_route = []
    remaining_bins = list(critical_bins)
    curr_lat, curr_lon = base_lat, base_lon
    
    total_distance = 0
    
    while remaining_bins:
        nearest_bin = min(remaining_bins, key=lambda b: calculate_distance(curr_lat, curr_lon, b.lat, b.long))
        dist = calculate_distance(curr_lat, curr_lon, nearest_bin.lat, nearest_bin.long)
        total_distance += dist
        
        optimized_route.append(nearest_bin)
        curr_lat, curr_lon = nearest_bin.lat, nearest_bin.long
        remaining_bins.remove(nearest_bin)

    # Format for template
    bins_data = []
    # Add Base Station as start
    bins_data.append({
        'id': 0,
        'name': 'Pusat Pengolahan (Start)',
        'lat': base_lat,
        'long': base_lon,
        'type': 'start'
    })
    
    for b in optimized_route:
        bins_data.append({
            'id': b.id,
            'name': b.name,
            'lat': b.lat,
            'long': b.long,
            'organic': b.organic_level,
            'type': 'bin'
        })
    
    # Estimate time: 20km/h avg speed including pickup time
    estimated_time_hours = total_distance / 20
    hours = int(estimated_time_hours)
    minutes = int((estimated_time_hours - hours) * 60)
    
    context = {
        'bins': bins_data,
        'total_distance': round(total_distance, 1),
        'total_time': f"{hours} jam {minutes} min" if hours > 0 else f"{minutes} min"
    }
    return render(request, 'dashboard/route_map.html', context)

def air_monitoring(request):
    bins = Bin.objects.all()
    
    # Get latest pollution for each bin
    latest_pollution = {}
    for b in bins:
        latest_log = PollutionLog.objects.filter(bin=b).order_by('-timestamp').first()
        if latest_log:
            latest_pollution[b.id] = {
                'id': b.id,
                'bin_name': b.name,
                'mq135': latest_log.mq135_value,
                'timestamp': latest_log.timestamp,
                'lat': b.lat,
                'lng': b.long,
            }
            
    # Calculate counts
    counts = {'normal': 0, 'warning': 0, 'bad': 0}
    for p in latest_pollution.values():
        val = p['mq135']
        if val < 400: counts['normal'] += 1
        elif val < 700: counts['warning'] += 1
        else: counts['bad'] += 1
            
    logs = PollutionLog.objects.select_related('bin').order_by('-timestamp')[:50]
    
    context = {
        'logs': logs,
        'latest_pollution': latest_pollution.values(),
        'counts': counts,
    }
    return render(request, 'dashboard/air_monitoring.html', context)

def reward_management(request):
    users = BinUser.objects.order_by('-total_points')
    rewards = Reward.objects.all().order_by('-created_at')
    active_vouchers_count = Reward.objects.filter(status='Active').count()
    context = {
        'users': users,
        'rewards': rewards,
        'active_vouchers_count': active_vouchers_count,
    }
    return render(request, 'dashboard/reward_management.html', context)

def add_reward(request):
    if request.method == 'POST':
        nama = request.POST.get('nama_reward')
        poin = request.POST.get('syarat_point')
        kuota = request.POST.get('kuota')
        keterangan = request.POST.get('keterangan')
        kategori = request.POST.get('kategori')
        foto = request.FILES.get('foto_reward')
        
        Reward.objects.create(
            nama_reward=nama,
            syarat_point=poin,
            kuota=kuota,
            keterangan=keterangan,
            kategori=kategori,
            foto_reward=foto,
            status='Active'
        )
        return redirect('reward_management')
    return redirect('reward_management')

def delete_reward(request, reward_id):
    reward = get_object_or_404(Reward, id=reward_id)
    reward.delete()
    return redirect('reward_management')

def add_bin(request):
    if request.method == 'POST':
        name = request.POST.get('name')
        lat = request.POST.get('lat')
        lng = request.POST.get('lng')
        
        Bin.objects.create(
            name=name,
            lat=float(lat),
            long=float(lng),
            organic_level=0,
            inorganic_level=0
        )
        return redirect('bin_monitoring')
    return redirect('bin_monitoring')

def delete_bin(request, bin_id):
    bin_obj = get_object_or_404(Bin, id=bin_id)
    bin_obj.delete()
    return redirect('bin_monitoring')
