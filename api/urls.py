from django.urls import path
from . import views

urlpatterns = [
    path('register/', views.register, name='api_register'),
    path('login/', views.login, name='api_login'),
    path('identify-face-esp/', views.identify_face_esp, name='identify_face_esp'),
    path('process-trash-esp/', views.process_trash_esp, name='process_trash_esp'),
    path('trigger-session/', views.trigger_session, name='trigger_session'),
    path('check-command/', views.check_command, name='check_command'),
    path('check-result/', views.check_result, name='check_result'),
    path('telemetry/', views.telemetry, name='api_telemetry'),
    path('rewards/', views.get_rewards, name='api_rewards'),
    path('claim-reward/', views.claim_reward, name='api_claim_reward'),
    path('purchase-history/', views.get_purchase_history, name='api_purchase_history'),
    path('update-profile/', views.update_profile, name='api_update_profile'),
    path('user-info/', views.get_user_info, name='api_user_info'),
    path('bins/', views.get_bins, name='get_bins'),
    path('dashboard-stats/', views.get_dashboard_stats, name='get_dashboard_stats'),
]
