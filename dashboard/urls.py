from django.urls import path
from . import views

urlpatterns = [
    path('', views.dashboard, name='dashboard'),
    path('bin-monitoring/', views.bin_monitoring, name='bin_monitoring'),
    path('route-map/', views.route_map, name='route_map'),
    path('air-monitoring/', views.air_monitoring, name='air_monitoring'),
    path('reward-management/', views.reward_management, name='reward_management'),
    path('add-reward/', views.add_reward, name='add_reward'),
    path('delete-reward/<int:reward_id>/', views.delete_reward, name='delete_reward'),
    path('add-bin/', views.add_bin, name='add_bin'),
    path('delete-bin/<int:bin_id>/', views.delete_bin, name='delete_bin'),
]
