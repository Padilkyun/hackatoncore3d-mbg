import os
import tensorflow as tf
import joblib
import numpy as np
import pandas as pd
from ultralytics import YOLO
from deepface import DeepFace
import cv2

class AIHandler:
    def __init__(self):
        # Load YOLOv8 (Custom Model)
        print("Loading YOLOv8 Custom Model...")
        self.yolo_model = YOLO('model/modelklasifikasi.pt') 
        
        # Load LSTM
        print("Loading LSTM...")
        self.lstm_model = tf.keras.models.load_model('model/lstm_waste_model.h5')
        self.scaler_X = joblib.load('model/scaler_X.pkl')
        self.scaler_y = joblib.load('model/scaler_y.pkl')
        
        # DeepFace doesn't need explicit loading of a model file usually, 
        # but it downloads weights on first use.
        print("AI Models initialized.")

    def detect_waste(self, image_path):
        results = self.yolo_model(image_path)
        # Simplified logic: if we find certain classes, label as organic/inorganic
        # In a real scenario, you'd map YOLO classes to your specific needs.
        # For now, let's assume class 0 is 'organic' and 1 is 'inorganic' for demo.
        # Or just return the most confident label.
        for r in results:
            for c in r.boxes.cls:
                label = self.yolo_model.names[int(c)]
                return label # Return first detected object label
        return "unknown"

    def identify_face(self, image_path, db_path):
        try:
            results = DeepFace.find(img_path=image_path, db_path=db_path, enforce_detection=False)
            if len(results) > 0 and not results[0].empty:
                # Return the identity of the first match
                return results[0]['identity'][0]
            return None
        except Exception as e:
            print(f"Face ID Error: {e}")
            return None

    def predict_level(self, sensor_history):
        # sensor_history: list of 12 data points
        feature_names = ['hour_of_day', 'is_weekend', 'minutes_since_empty', 'fill_level_percent']
        df_input = pd.DataFrame(sensor_history, columns=feature_names)
        scaled_input = self.scaler_X.transform(df_input)
        X_inference = scaled_input.reshape(1, 12, 4)
        
        prediksi_scaled = self.lstm_model.predict(X_inference)
        prediksi_asli = self.scaler_y.inverse_transform(prediksi_scaled)
        return float(prediksi_asli[0][0])
