#include "esp_camera.h"
#include <WiFi.h>
#include <HTTPClient.h>
#include <WebServer.h>

// ================= WIFI =================

const char* ssid = "Redmi 10";
const char* password = "insyaallah";

// ================= SERVER =================

const char* serverBase = "http://10.162.1.144/api"; // Ganti dengan IP Laptop Anda
int binID = 6;

WebServer server(80);

// ================= CAMERA PIN =================

#define PWDN_GPIO_NUM     32
#define RESET_GPIO_NUM    -1
#define XCLK_GPIO_NUM      0
#define SIOD_GPIO_NUM     26
#define SIOC_GPIO_NUM     27

#define Y9_GPIO_NUM       35
#define Y8_GPIO_NUM       34
#define Y7_GPIO_NUM       39
#define Y6_GPIO_NUM       36
#define Y5_GPIO_NUM       21
#define Y4_GPIO_NUM       19
#define Y3_GPIO_NUM       18
#define Y2_GPIO_NUM        5

#define VSYNC_GPIO_NUM    25
#define HREF_GPIO_NUM     23
#define PCLK_GPIO_NUM     22

String captureAndUpload(String endpoint);

// ================= HANDLERS =================

void handleCam() {
  camera_fb_t * fb = esp_camera_fb_get();
  if (!fb) {
    server.send(500, "text/plain", "Camera capture failed");
    return;
  }
  
  WiFiClient client = server.client();
  String response = "HTTP/1.1 200 OK\r\n";
  response += "Content-Type: image/jpeg\r\n";
  response += "Content-Length: " + String(fb->len) + "\r\n";
  response += "Access-Control-Allow-Origin: *\r\n";
  response += "Cache-Control: no-store, no-cache, must-revalidate\r\n";
  response += "Connection: close\r\n\r\n";
  
  client.write((const uint8_t *)response.c_str(), response.length());
  client.write(fb->buf, fb->len);
  
  esp_camera_fb_return(fb);
}

// ================= SETUP =================

void setup() {
  Serial.begin(115200);

  WiFi.begin(ssid, password);
  while(WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi Connected");
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());

  camera_config_t config;
  config.ledc_channel = LEDC_CHANNEL_0;
  config.ledc_timer = LEDC_TIMER_0;
  config.pin_d0 = Y2_GPIO_NUM;
  config.pin_d1 = Y3_GPIO_NUM;
  config.pin_d2 = Y4_GPIO_NUM;
  config.pin_d3 = Y5_GPIO_NUM;
  config.pin_d4 = Y6_GPIO_NUM;
  config.pin_d5 = Y7_GPIO_NUM;
  config.pin_d6 = Y8_GPIO_NUM;
  config.pin_d7 = Y9_GPIO_NUM;
  config.pin_xclk = XCLK_GPIO_NUM;
  config.pin_pclk = PCLK_GPIO_NUM;
  config.pin_vsync = VSYNC_GPIO_NUM;
  config.pin_href = HREF_GPIO_NUM;
  config.pin_sscb_sda = SIOD_GPIO_NUM;
  config.pin_sscb_scl = SIOC_GPIO_NUM;
  config.pin_pwdn = PWDN_GPIO_NUM;
  config.pin_reset = RESET_GPIO_NUM;
  config.xclk_freq_hz = 20000000;
  config.pixel_format = PIXFORMAT_JPEG;
  config.frame_size = FRAMESIZE_VGA;
  config.jpeg_quality = 10;
  config.fb_count = 1;

  esp_err_t err = esp_camera_init(&config);
  if (err != ESP_OK) {
    Serial.printf("Camera init failed with error 0x%x", err);
    return;
  }

  // Setup Web Server
  server.on("/cam-hi.jpg", HTTP_GET, handleCam);
  server.begin();
}

// ================= LOOP =================

void loop() {
  // Melayani permintaan preview dari Android
  server.handleClient();

  // Mengecek perintah dari ESP32 Wroom
  if(Serial.available()) {
    String command = Serial.readStringUntil('\n');
    command.trim();

    if(command == "FACE") {
      String result = captureAndUpload("identify-face-esp");
      Serial.println(result); // Return SUCCESS:username atau FAILED
    }
    else if(command == "TRASH") {
      String result = captureAndUpload("process-trash-esp");
      Serial.println(result); // Return ORGANIK atau NON ORGANIK
    }
  }
}

// ================= CAPTURE =================

String captureAndUpload(String endpoint) {
  camera_fb_t * fb = esp_camera_fb_get();
  if(!fb) return "ERROR:Camera Fail";

  HTTPClient http;
  String fullUrl = String(serverBase) + "/" + endpoint + "/?bin_id=" + String(binID);
  
  http.begin(fullUrl);
  http.addHeader("Content-Type", "image/jpeg");

  int httpResponseCode = http.POST(fb->buf, fb->len);
  String payload = "ERROR:Http Fail";
  
  if (httpResponseCode > 0) {
    payload = http.getString();
  }
  
  esp_camera_fb_return(fb);
  http.end();
  return payload;
}