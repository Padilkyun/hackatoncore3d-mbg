# 🌍 Meta Bin Go (Smart Waste Management System)

Selamat datang di repositori **Meta Bin Go**, sebuah solusi terintegrasi untuk manajemen sampah pintar. Proyek ini menggabungkan perangkat keras IoT (Internet of Things), Kecerdasan Buatan (AI), Aplikasi Mobile, dan Dashboard Web untuk menciptakan ekosistem pembuangan sampah yang modern, efisien, dan menyenangkan (gamifikasi).

---

## 🎯 Fitur Utama

### 1. 🤖 AI-Powered Trash Sorting (YOLOv8)
Tempat sampah dilengkapi dengan kamera ESP32-CAM yang akan memotret sampah Anda. Gambar tersebut dikirim ke server pusat untuk diproses menggunakan model **YOLOv8 Custom** (`modelklasifikasi.pt`) yang secara cerdas dapat membedakan sampah **Organik** dan **Non-Organik**. Setelah dikenali, motor servo di dalam tempat sampah akan otomatis memilah sampah tersebut ke wadah yang benar.

### 2. 📱 Aplikasi Mobile (Flutter) & Gamifikasi
Tidak perlu lagi menyentuh tempat sampah! Pengguna dapat mengontrol tempat sampah langsung dari *smartphone* Android. 
* **Live Camera Preview:** Lihat isi kamera tempat sampah secara *real-time* di layar HP Anda sebelum membuang sampah.
* **Sistem Poin (Rewards):** Setiap kali berhasil membuang sampah, akun Anda akan otomatis mendapatkan tambahan poin (+10 Poin). Poin ini dapat ditukarkan dengan berbagai hadiah menarik!
* **Peta Lokasi & Polusi:** Cari tempat sampah terdekat dan lihat status kualitas udara di sekitarnya.

### 3. ☁️ Web Dashboard & API Server (Django + Python)
Server sentral yang mengatur seluruh aliran data, menyajikan antarmuka visual (Dashboard) untuk petugas/admin:
* **Monitoring Real-time:** Pantau kapasitas kepenuhan sampah (menggunakan sensor Ultrasonik) dan kualitas udara/gas metana (menggunakan sensor MQ-135).
* **Manajemen Pengguna & Hadiah:** Admin dapat melihat daftar pengguna aktif dan menyetujui penukaran hadiah.
* **REST API:** Menjadi jembatan komunikasi yang mulus antara Aplikasi Android dan perangkat keras IoT ESP32.

---

## 🛠️ Arsitektur Teknologi

* **Perangkat Keras (IoT):** 
  * NodeMCU ESP32 (Sebagai otak utama pengendali servo, sensor, dan komunikasi internet)
  * ESP32-CAM (Sebagai modul kamera mandiri)
  * Sensor Ultrasonik HC-SR04 (Mendeteksi kapasitas tempat sampah)
  * Sensor Gas MQ-135 (Mendeteksi kualitas udara)
  * Motor Servo (Untuk membuka/menutup dan memilah tong sampah)
* **Backend & AI Server:**
  * Python dengan framework **Django**
  * **YOLOv8** (Ultralytics) untuk Object Detection / Klasifikasi Sampah
  * SQLite (Database)
* **Frontend (Aplikasi Mobile):**
  * **Flutter** (Dart) untuk Android
* **Frontend (Web Dashboard):**
  * HTML, CSS, JavaScript dengan Bootstrap
  * Django Templates

---

## 🚀 Panduan Instalasi & Penggunaan

### 1. Persiapan Server Django (Backend & AI)
1. Pastikan Anda telah menginstal Python 3.9+
2. Buka terminal di folder root (`D:\hackaton3d`) dan aktifkan Virtual Environment:
   ```bash
   .\venv\Scripts\activate
   ```
3. Install semua *requirements* (jika belum):
   ```bash
   pip install -r requirements.txt
   ```
4. Jalankan server (Pastikan berjalan di IP lokal Anda, misalnya `0.0.0.0:8000`):
   ```bash
   python manage.py runserver 0.0.0.0:8000
   ```
   *Catatan: Saat server pertama kali berjalan, model AI YOLO akan dimuat ke dalam memori.*

### 2. Persiapan IoT (ESP32 & ESP32-CAM)
1. Buka file `kodinganespbiasa.cp` dan `kodinganespcam.cpp` menggunakan Arduino IDE.
2. Di bagian paling atas kode, ubah kredensial WiFi (`ssid` dan `password`) agar sesuai dengan *hotspot* atau WiFi rumah Anda.
3. Pastikan `serverBase` di ESP32 Biasa mengarah ke IP Laptop/Server Django Anda (contoh: `http://10.162.1.144:8000/api`).
4. *Flash* (Upload) kode tersebut ke masing-masing *board* ESP. Catat IP Address yang didapat oleh ESP32-CAM melalui *Serial Monitor*.

### 3. Persiapan Aplikasi Android (Flutter)
1. Masuk ke folder `kodinganandroid`.
2. Buka `lib/services/api_service.dart` dan ubah variabel `baseUrl` dengan IP Server Django Anda.
3. Buka `lib/screens/home_screen.dart` dan ubah variabel `espCamIp` dengan IP Address ESP32-CAM Anda (contoh: `http://10.162.1.205`).
4. Jalankan aplikasi ke perangkat Android Anda:
   ```bash
   flutter run
   ```

---

## 💡 Alur Kerja (Workflow) Pembuangan Sampah

1. Pengguna membuka aplikasi Meta Bin Go dan menekan tombol **"Buang Sampah"**.
2. Aplikasi menampilkan **Live Preview** dari ESP32-CAM.
3. Pengguna menempatkan sampah di depan kamera dan menekan **"Mulai Scan"**.
4. Aplikasi mengirim `username` ke Server Django dan "memesan" sesi tempat sampah.
5. ESP32 Biasa (yang terus memantau perintah dari server) mendeteksi pesanan tersebut, lalu mengirimkan perintah `TRASH` ke ESP32-CAM melalui komunikasi *Serial*.
6. ESP32-CAM mengambil foto resolusi tinggi dan mengirimkannya ke Server Django.
7. Model **YOLOv8** di Server mengklasifikasikan gambar (Organik / Non-Organik) dan membalas ESP32.
8. ESP32 Biasa menggerakkan servo pemilah sesuai hasil dari AI.
9. Server secara otomatis **menambahkan +10 Poin** ke akun pengguna.
10. Aplikasi Android mendeteksi hasil dari server dan menampilkan pesan keberhasilan.

---

## 🤝 Kontribusi
Proyek ini dibangun sebagai purwarupa (prototype) untuk acara *Hackathon*. Segala bentuk modifikasi, pengembangan, dan perbaikan sangat dipersilakan untuk mewujudkan lingkungan yang lebih bersih dan pintar!

*Let's build a greener future together! 🌱*
