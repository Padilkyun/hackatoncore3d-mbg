from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from typing import List
import numpy as np
import pandas as pd
import joblib
import os
import tensorflow as tf


app = FastAPI(title="Waste Bin Prediction API", version="1.0")

MODEL_PATH = "lstm_waste_model.h5"
SCALER_X_PATH = "scaler_X.pkl"
SCALER_Y_PATH = "scaler_y.pkl"

if not (os.path.exists(MODEL_PATH) and os.path.exists(SCALER_X_PATH) and os.path.exists(SCALER_Y_PATH)):
    raise RuntimeError("File Model atau Scaler tidak ditemukan. Pastikan file .h5 dan .pkl tersedia.")

print("Memuat Model LSTM dan Scaler ke dalam memori...")
model = tf.keras.models.load_model(MODEL_PATH)
scaler_X = joblib.load(SCALER_X_PATH)
scaler_y = joblib.load(SCALER_Y_PATH)
print("Model siap menerima inference!")

class SensorData(BaseModel):
    hour_of_day: int
    is_weekend: int
    minutes_since_empty: int
    fill_level_percent: float

class InferenceRequest(BaseModel):
    sensor_history: List[SensorData]

@app.post("/predict")
async def predict_fill_level(request: InferenceRequest):
    if len(request.sensor_history) != 12:
        raise HTTPException(status_code=400, detail="Riwayat sensor harus berisi tepat 12 data (3 jam terakhir).")
    
    try:
        raw_data = [
            [
                data.hour_of_day, 
                data.is_weekend, 
                data.minutes_since_empty, 
                data.fill_level_percent
            ] 
            for data in request.sensor_history
        ]
        feature_names = ['hour_of_day', 'is_weekend', 'minutes_since_empty', 'fill_level_percent']
        df_input = pd.DataFrame(raw_data, columns=feature_names)

        scaled_input = scaler_X.transform(df_input)

        X_inference = scaled_input.reshape(1, 12, 4)
        
        prediksi_scaled = model.predict(X_inference)

        prediksi_asli = scaler_y.inverse_transform(prediksi_scaled)
        hasil_persentase = float(prediksi_asli[0][0])

        butuh_pickup = bool(hasil_persentase >= 85.0)
        
        return {
            "status": "success",
            "predicted_next_15m_percent": round(hasil_persentase, 2),
            "pickup_recommended": butuh_pickup,
            "message": "Tong sampah akan segera penuh" if butuh_pickup else "Kapasitas masih aman"
        }
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Terjadi kesalahan saat inferensi: {str(e)}")

@app.get("/ping")
async def ping():
    return {"status": "Sistem Inferensi Aktif"}