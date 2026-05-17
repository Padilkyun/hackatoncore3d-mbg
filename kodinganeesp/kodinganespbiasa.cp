#include <WiFi.h>
#include <HTTPClient.h>
#include <ESP32Servo.h>
#include <LiquidCrystal_I2C.h>

// ================= WIFI =================

const char* ssid = "NAMA_WIFI";
const char* password = "PASSWORD_WIFI";
const char* serverBase = "http://10.162.1.144:8000/api"; // Ganti dengan IP Laptop Anda
int binID = 6;

// ================= LCD =================

LiquidCrystal_I2C lcd(0x27, 16, 2);

// ================= SERVO =================

Servo servoFilter;
Servo servoCam;

// ================= PIN =================

#define FILTER_SERVO 18 
#define CAM_SERVO 19

#define BUTTON_PIN 13

#define LED_HIJAU 4
#define LED_KUNING 5
#define LED_MERAH 23

#define TRIG_SAMPAH 14
#define ECHO_SAMPAH 27

#define MQ135_PIN 34

// ================= VARIABEL =================

String hasilAI = "";
bool sesiAktif = false;
int persenSampah = 0;
int airValue = 0;
String statusUdara = "";
unsigned long lastTelemetry = 0;

// ================= SETUP =================

void setup() {
  Serial.begin(115200);
  Serial2.begin(115200); // Komunikasi ke ESP-CAM

  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi Connected");

  pinMode(BUTTON_PIN, INPUT_PULLUP);
  pinMode(LED_HIJAU, OUTPUT);
  pinMode(LED_KUNING, OUTPUT);
  pinMode(LED_MERAH, OUTPUT);
  pinMode(TRIG_SAMPAH, OUTPUT);
  pinMode(ECHO_SAMPAH, INPUT);

  servoFilter.setPeriodHertz(50);
  servoCam.setPeriodHertz(50);
  servoFilter.attach(FILTER_SERVO, 500, 2400);
  servoCam.attach(CAM_SERVO, 500, 2400);

  lcd.init();
  lcd.backlight();

  standbyMode();
}

// ================= LOOP =================

void loop() {
  cekUdara();
  cekLevelSampah();

  // Kirim Telemetry setiap 5 detik
  if (millis() - lastTelemetry > 5000) {
    sendTelemetry();
    lastTelemetry = millis();
  }

  // Cek perintah dari Android setiap 2 detik
  static unsigned long lastCheckCommand = 0;
  if (millis() - lastCheckCommand > 2000 && !sesiAktif && persenSampah < 80) {
    checkRemoteCommand();
    lastCheckCommand = millis();
  }

  if(persenSampah >= 80) {
    modePenuh();
    return;
  }

  if(digitalRead(BUTTON_PIN) == LOW && !sesiAktif) {
    delay(300);
    sesiAktif = true;
    mulaiSesi();
  }
  else if(digitalRead(BUTTON_PIN) == LOW && sesiAktif) {
    delay(300);
    selesaiSesi();
    sesiAktif = false;
  }
}

// ================= TELEMETRY =================

void sendTelemetry() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = String(serverBase) + "/telemetry/";
    http.begin(url);
    http.addHeader("Content-Type", "application/json");

    String json = "{\"bin_id\": " + String(binID) + 
                  ", \"organic_level\": " + String(persenSampah) + 
                  ", \"inorganic_level\": 0, \"mq135_value\": " + String(airValue) + "}";

    int httpResponseCode = http.POST(json);
    http.end();
  }
}

// ================= REMOTE COMMAND =================

void checkRemoteCommand() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = String(serverBase) + "/check-command/?bin_id=" + String(binID);
    http.begin(url);
    int httpCode = http.GET();
    
    if (httpCode > 0) {

      String payload = http.getString();
      payload.trim();
      if (payload == "TRIGGER:1") {
        sesiAktif = true;
        mulaiSesi();
      }
    }
    http.end();
  }
}

// ================= STANDBY =================

void standbyMode() {
  digitalWrite(LED_HIJAU, HIGH);
  digitalWrite(LED_KUNING, LOW);
  digitalWrite(LED_MERAH, LOW);
  servoCam.write(90);
  servoFilter.write(90);
  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print("SMART BIN");
  lcd.setCursor(0,1);
  lcd.print("READY");
}

// ================= MULAI SESI =================

void mulaiSesi() {
  digitalWrite(LED_HIJAU, LOW);
  digitalWrite(LED_KUNING, HIGH);

  // LANGSUNG SCAN SAMPAH
  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print("SCAN SAMPAH");
  servoCam.write(175);
  delay(2000);
  Serial2.println("TRASH");

  while(Serial2.available() == 0) { delay(10); }
  hasilAI = Serial2.readStringUntil('\n');
  hasilAI.trim();

  prosesSampah();
  
  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print("TERIMA KASIH");
  lcd.setCursor(0,1);
  lcd.print("+10 POINTS");

  delay(3000);
  selesaiSesi();
  sesiAktif = false;
}

// ================= PROSES SAMPAH =================

void prosesSampah() {
  lcd.clear();
  if(hasilAI == "ORGANIK") {
    lcd.setCursor(0,0);
    lcd.print("ORGANIK");
    servoFilter.write(30);
  }
  else {
    lcd.setCursor(0,0);
    lcd.print("NON ORGANIK");
    servoFilter.write(150);
  }
  delay(3000);
  servoFilter.write(90);
}

// ================= SELESAI =================

void selesaiSesi() {
  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print("SESI SELESAI");
  digitalWrite(LED_KUNING, LOW);
  digitalWrite(LED_HIJAU, HIGH);
  servoCam.write(90);
  servoFilter.write(90);
  delay(3000);
  standbyMode();
}

// ================= MQ135 =================

void cekUdara() {
  airValue = analogRead(MQ135_PIN);
  if(airValue < 400) statusUdara = "NORMAL";
  else if(airValue < 700) statusUdara = "WARNING";
  else statusUdara = "BAD";
}

// ================= HC-SR04 =================

long bacaJarak() {
  digitalWrite(TRIG_SAMPAH, LOW);
  delayMicroseconds(2);
  digitalWrite(TRIG_SAMPAH, HIGH);
  delayMicroseconds(10);
  digitalWrite(TRIG_SAMPAH, LOW);
  long durasi = pulseIn(ECHO_SAMPAH, HIGH);
  long jarak = durasi * 0.034 / 2;
  return jarak;
}

// ================= LEVEL SAMPAH =================

void cekLevelSampah() {
  long jarak = bacaJarak();
  persenSampah = map(jarak, 28, 0, 0, 100);
  persenSampah = constrain(persenSampah, 0, 100);
}

// ================= FULL =================

void modePenuh() {
  digitalWrite(LED_HIJAU, LOW);
  digitalWrite(LED_KUNING, LOW);
  digitalWrite(LED_MERAH, HIGH);
  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print("SAMPAH PENUH");
  lcd.setCursor(0,1);
  lcd.print(String(persenSampah) + "%");
  delay(1000);
}