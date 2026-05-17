import os
import django
import datetime

# Set up Django environment
os.environ.setdefault('DJANGO_SETTINGS_MODULE', 'metabingo_django.settings')
django.setup()

from dashboard.models import BinUser, Bin, PollutionLog, Reward

def seed():
    # 1. Create Bins
    bins_data = [
        {"name": "Bin Simpang Haru", "lat": -0.9535, "long": 100.3615, "organic": 85.0, "inorganic": 40.0},
        {"name": "Bin Pondok", "lat": -0.9520, "long": 100.3630, "organic": 45.0, "inorganic": 20.0},
        {"name": "Bin Ulak Karang", "lat": -0.9250, "long": 100.3550, "organic": 92.0, "inorganic": 60.0},
        {"name": "Bin Lubuk Buaya", "lat": -0.8250, "long": 100.3350, "organic": 30.0, "inorganic": 10.0},
    ]
    
    bins = []
    for data in bins_data:
        b, created = Bin.objects.get_or_create(
            name=data["name"],
            defaults={
                "lat": data["lat"],
                "long": data["long"],
                "organic_level": data["organic"],
                "inorganic_level": data["inorganic"]
            }
        )
        bins.append(b)
        print(f"Bin {b.name} created/exists.")

    # 2. Create Users
    users_data = [
        {"username": "Fadhillah Rahmad Kurnia", "points": 5010},
        {"username": "Hanaviz", "points": 4870},
        {"username": "Widia Khairunisa", "points": 4515},
    ]
    
    for data in users_data:
        u, created = BinUser.objects.get_or_create(
            username=data["username"],
            defaults={"total_points": data["points"]}
        )
        print(f"User {u.username} created/exists.")

    # 3. Create Pollution Logs
    for b in bins:
        PollutionLog.objects.create(
            bin=b,
            mq135_value=80 if b.organic_level < 80 else 250,
            timestamp=datetime.datetime.now()
        )
    print("Pollution logs created.")

    # 4. Create Rewards
    rewards_data = [
        {"name": "Voucher GrabFood 20K", "points": 2000, "kuota": 50, "desc": "Potongan makan di GrabFood."},
        {"name": "Voucher GoRide 10K", "points": 1000, "kuota": 100, "desc": "Potongan perjalanan GoRide."},
    ]
    
    for data in rewards_data:
        Reward.objects.get_or_create(
            nama_reward=data["name"],
            defaults={
                "syarat_point": data["points"],
                "kuota": data["kuota"],
                "keterangan": data["desc"],
                "status": "Active"
            }
        )
        print(f"Reward {data['name']} created/exists.")

if __name__ == "__main__":
    seed()
