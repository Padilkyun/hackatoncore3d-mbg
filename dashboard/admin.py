from django.contrib import admin
from .models import BinUser, Bin, PollutionLog, Reward

admin.site.register(BinUser)
admin.site.register(Bin)
admin.site.register(PollutionLog)
admin.site.register(Reward)
