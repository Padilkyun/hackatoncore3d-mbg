from django.db import models
from django.utils import timezone

class BinUser(models.Model):
    username = models.CharField(max_length=150, unique=True)
    password = models.CharField(max_length=128, null=True, blank=True) # Simple for now
    face_embedding = models.JSONField(null=True, blank=True)  # Store face embeddings for comparison
    total_points = models.IntegerField(default=0)
    profile_picture = models.ImageField(upload_to='profiles/', null=True, blank=True)

    def __str__(self):
        return self.username

class Bin(models.Model):
    name = models.CharField(max_length=255)
    lat = models.FloatField()
    long = models.FloatField()
    organic_level = models.FloatField(default=0.0)
    inorganic_level = models.FloatField(default=0.0)
    last_update = models.DateTimeField(default=timezone.now)
    predicted_full_time = models.DateTimeField(null=True, blank=True)
    active_user = models.ForeignKey(BinUser, on_delete=models.SET_NULL, null=True, blank=True)
    last_session_time = models.DateTimeField(null=True, blank=True)
    trigger_session = models.BooleanField(default=False)
    last_scan_result = models.CharField(max_length=50, null=True, blank=True)

    def __str__(self):
        return self.name

class PollutionLog(models.Model):
    bin = models.ForeignKey(Bin, on_delete=models.CASCADE, related_name='pollution_logs')
    mq135_value = models.IntegerField()
    timestamp = models.DateTimeField(default=timezone.now)

    def __str__(self):
        return f"Log for {self.bin.name} at {self.timestamp}"

class Reward(models.Model):
    STATUS_CHOICES = [
        ('Active', 'Active'),
        ('Inactive', 'Inactive'),
    ]
    CATEGORY_CHOICES = [
        ('Semua', 'Semua'),
        ('Transportasi', 'Transportasi'),
        ('Belanja', 'Belanja'),
        ('Makanan', 'Makanan'),
    ]
    nama_reward = models.CharField(max_length=255)
    syarat_point = models.IntegerField()
    kuota = models.IntegerField()
    foto_reward = models.ImageField(upload_to='rewards/', null=True, blank=True)
    keterangan = models.TextField()
    kategori = models.CharField(max_length=50, choices=CATEGORY_CHOICES, default='Semua')
    status = models.CharField(max_length=20, choices=STATUS_CHOICES, default='Active')
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    def __str__(self):
        return self.nama_reward

class ClaimedReward(models.Model):
    user = models.ForeignKey(BinUser, on_delete=models.CASCADE, related_name='claimed_rewards')
    reward = models.ForeignKey(Reward, on_delete=models.CASCADE)
    claimed_at = models.DateTimeField(auto_now_add=True)
    
    def __str__(self):
        return f"{self.user.username} claimed {self.reward.nama_reward}"
