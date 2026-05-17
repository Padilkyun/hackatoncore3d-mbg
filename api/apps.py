from django.apps import AppConfig
import os

class ApiConfig(AppConfig):
    default_auto_field = 'django.db.models.BigAutoField'
    name = 'api'
    ai_handler = None

    def ready(self):
        # Prevent double initialization in dev server
        if os.environ.get('RUN_MAIN') == 'true':
            try:
                from ai_handler import AIHandler
                ApiConfig.ai_handler = AIHandler()
                print("AI Handler initialized successfully in API app.")
            except Exception as e:
                print(f"Failed to initialize AI Handler: {e}")
