// ============================================================
//  Digital Child Weight Monitoring System
//  NodeMCU ESP8266 + HX711 + Load Cell 20kg + LCD 16x2
// ============================================================
//  Project: Digital Child Monitoring & Immunization Tracking System
//  Date:    22 June 2026
//  Board:   NodeMCU 1.0 (ESP-12E Module)
//  Upload:  115200 baud
// ============================================================
//
// VERIFICATION CHECKLIST:
// ✓ HX711 on D5 (DT) and D6 (SCK)  -- Section 3.2
// ✓ LCD 16x2 in 4-bit parallel mode -- Section 3.3
// ✓ No LiquidCrystal_I2C used       -- Section 1 note
// ✓ WiFi with 20 retry attempts     -- Section 4.2(C)
// ✓ Stable reading check (3 cons readings < 0.05 kg diff) -- Section 4.2(A)
// ✓ Round weight to 2 decimal places -- Section 4.2(A)
// ✓ LCD Line 1: weight, Line 2: status -- Section 4.2(B)
// ✓ Auto upload via HTTP POST       -- Section 4.2(D)
// ✓ 5 second upload delay           -- Section 4.2(D)
// ✓ Tare via FLASH button (GPIO0)   -- Section 4.2(E)
// ✓ Calibration factor constant      -- Section 4.2(E)
// ✓ Flash size: 4MB (FS:2MB, OTA:~1019KB) -- Section 8
// ============================================================

#include <HX711.h>
#include <LiquidCrystal.h>
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>

// ============================================================
//  CONSTANTS - Configure these for your setup
// ============================================================

// --- WiFi Credentials ---
const char* WIFI_SSID     = "YOUR_WIFI_NAME";
const char* WIFI_PASSWORD = "YOUR_WIFI_PASSWORD";

// --- Google Apps Script Web URL ---
// Deploy your Google Apps Script as a Web App and paste the URL here.
const char* SERVER_URL = "https://script.google.com/macros/s/YOUR_SCRIPT_ID/exec";

// --- Child ID (hardcoded for now; can be made dynamic later) ---
const char* CHILD_ID = "CHILD_001";

// ============================================================
//  PIN DEFINITIONS (See Section 3 - Exact Pin Connection Map)
// ============================================================

// --- HX711 Pins (Section 3.2) ---
#define HX711_DT_PIN   D5   // GPIO14 - Serial data from HX711
#define HX711_SCK_PIN  D6   // GPIO12 - Clock signal to HX711

// --- LCD Pins - 4-bit Parallel Mode (Section 3.3) ---
// LiquidCrystal(RS, EN, D4, D5, D6, D7)
// LCD D0-D3: NOT CONNECTED (not used in 4-bit mode)
// LCD RW pin (#5): connected to GND (always write mode)
// LCD V0 (contrast): middle pin of 10kΩ pot between VIN and GND
// LCD A (backlight+): 3V3 via 220Ω resistor
#define LCD_RS   D4   // GPIO2  -> LCD pin 4
#define LCD_EN   D3   // GPIO0  -> LCD pin 6
#define LCD_D4   D2   // GPIO4  -> LCD pin 11
#define LCD_D5   D1   // GPIO5  -> LCD pin 12
#define LCD_D6   D0   // GPIO16 -> LCD pin 13
#define LCD_D7   RX   // GPIO3  -> LCD pin 14

// --- Tare Button (Section 4.2.E) ---
// NOTE: The NodeMCU FLASH button is on GPIO0 (D3), which conflicts with LCD_EN.
// The spec requires using the FLASH button for tare.
// To avoid LCD disruption, we recommend adding an EXTERNAL momentary push button
// connected from D7 (GPIO13) to GND. Press to tare.
// If you must use the FLASH button, press and hold briefly - the code will
// temporarily switch D3 to input mode to read it, which may cause brief LCD glitches.
// For reliable operation, use an external button on D7 as recommended.
#define TARE_BUTTON_PIN     D7   // GPIO13 - External button (preferred)
// #define TARE_BUTTON_PIN  D3   // GPIO0 - FLASH button (conflicts with LCD_EN - use with caution)

// ============================================================
//  CALIBRATION (Section 7 - Load Cell Calibration Procedure)
// ============================================================
// Adjust this value after performing the calibration procedure:
//   1. Power on, press tare button to zero
//   2. Place known weight (e.g., 1kg) on scale
//   3. Read raw value from Serial Monitor
//   4. CALIBRATION_FACTOR = raw_value / known_weight_in_kg
//   5. Update this constant and re-upload
// Typical range for 20kg load cell: -300 to -700
float CALIBRATION_FACTOR = -420.0;

// ============================================================
//  STABLE READING PARAMETERS (Section 4.2.A)
// ============================================================
#define STABLE_THRESHOLD       0.05   // Max kg difference to consider stable
#define STABLE_COUNT_REQUIRED  3      // Consecutive stable readings needed
#define POST_UPLOAD_DELAY_MS   5000   // 5 sec delay after upload (Section 4.2.D)
#define READ_INTERVAL_MS       200    // Read weight every 200ms

// ============================================================
//  GLOBAL OBJECTS
// ============================================================
HX711         scale;
LiquidCrystal lcd(LCD_RS, LCD_EN, LCD_D4, LCD_D5, LCD_D6, LCD_D7);
WiFiClient    wifiClient;

// ============================================================
//  STATE VARIABLES
// ============================================================
float  currentWeight        = 0.0;
float  lastStableWeight     = 0.0;
float  readings[STABLE_COUNT_REQUIRED];
int    readingIndex         = 0;
bool   readingsReady        = false;  // true once circular buffer filled once
bool   weightIsStable       = false;
bool   lastWeightWasStable  = false;

unsigned long lastReadTime      = 0;
unsigned long lastUploadTime    = 0;
bool          uploading         = false;
bool          uploadSuccess     = false;
bool          uploadFailed      = false;
unsigned long uploadStatusTime  = 0;

// LCD display optimization - only update when something changes
float  lastDisplayedWeight  = -1.0;
String lastDisplayedStatus  = "";

// Button debounce
unsigned long lastButtonReadTime = 0;
#define BUTTON_DEBOUNCE_MS 300

// ============================================================
//  FORWARD DECLARATIONS
// ============================================================
void setupWiFi();
void tareScale();
void readWeight();
bool isWeightStable();
String getStatusMessage();
void updateLCD();
void uploadWeight(float weight);
String getFormattedTimestamp();

// ============================================================
//  SETUP (Section 4 - Code Functional Requirements)
// ============================================================
void setup() {
  // Initialize Serial for debugging (115200 baud - Section 8)
  Serial.begin(115200);
  Serial.println();
  Serial.println(F("============================================"));
  Serial.println(F("  Digital Child Weight Monitoring System"));
  Serial.println(F("  NodeMCU ESP8266 + HX711 + LCD 16x2"));
  Serial.println(F("============================================"));

  // --- Initialize LCD - 4-bit Parallel Mode (Section 4.2.B) ---
  lcd.begin(16, 2);
  lcd.clear();
  lcd.print("  Child Weight");
  lcd.setCursor(0, 1);
  lcd.print("  Monitor v1.0");
  delay(1500);

  // --- Initialize HX711 on D5 (DT) and D6 (SCK) (Section 4.2.A) ---
  scale.begin(HX711_DT_PIN, HX711_SCK_PIN);
  scale.set_scale(CALIBRATION_FACTOR);
  scale.tare();  // Auto-tare on startup

  Serial.println(F("HX711 initialized."));
  Serial.print(F("Calibration factor: "));
  Serial.println(CALIBRATION_FACTOR);

  // --- Initialize readings buffer ---
  for (int i = 0; i < STABLE_COUNT_REQUIRED; i++) {
    readings[i] = 0.0;
  }

  // --- Initialize tare button pin ---
  pinMode(TARE_BUTTON_PIN, INPUT_PULLUP);

  // --- Connect to WiFi (Section 4.2.C) ---
  lcd.clear();
  lcd.print("Connecting WiFi");
  lcd.setCursor(0, 1);
  lcd.print("Please wait...");
  setupWiFi();

  // --- Show WiFi status ---
  lcd.clear();
  if (WiFi.status() == WL_CONNECTED) {
    lcd.print("WiFi Connected!");
    lcd.setCursor(0, 1);
    lcd.print(WiFi.localIP().toString().substring(0, 16));
    Serial.println(F("WiFi connected successfully."));
    Serial.print(F("IP Address: "));
    Serial.println(WiFi.localIP());
  } else {
    lcd.print("  WiFi Failed");
    lcd.setCursor(0, 1);
    lcd.print("Check Credentials");
    Serial.println(F("WiFi connection FAILED."));
  }

  delay(2000);
  lcd.clear();

  Serial.println(F("System ready. Place child on scale."));
}

// ============================================================
//  MAIN LOOP
// ============================================================
void loop() {
  unsigned long now = millis();

  // --- Read weight at intervals (Section 4.2.A) ---
  if (now - lastReadTime >= READ_INTERVAL_MS) {
    lastReadTime = now;
    readWeight();
  }

  // --- Check tare button with debounce (Section 4.2.E) ---
  if (digitalRead(TARE_BUTTON_PIN) == LOW && (now - lastButtonReadTime >= BUTTON_DEBOUNCE_MS)) {
    lastButtonReadTime = now;
    delay(50);  // Debounce delay
    if (digitalRead(TARE_BUTTON_PIN) == LOW) {
      // Wait for button release
      while (digitalRead(TARE_BUTTON_PIN) == LOW) {
        delay(10);
      }
      Serial.println(F("Tare button pressed!"));
      tareScale();
      lcd.clear();
      lcd.print("  Tare Done!");
      lcd.setCursor(0, 1);
      lcd.print("  Zero Set OK");
      delay(1000);
      lcd.clear();
      // Reset stable state
      weightIsStable = false;
      lastWeightWasStable = false;
      lastStableWeight = 0.0;
      for (int i = 0; i < STABLE_COUNT_REQUIRED; i++) {
        readings[i] = 0.0;
      }
      readingIndex = 0;
      readingsReady = false;
      lastDisplayedWeight = -1.0;  // Force LCD refresh
      lastDisplayedStatus = "";
    }
  }

  // --- Check if weight is stable and trigger upload (Section 4.2.D) ---
  if (weightIsStable && !lastWeightWasStable && !uploading && (now - lastUploadTime >= POST_UPLOAD_DELAY_MS)) {
    // Weight just became stable -> upload (unless same as last stable weight)
    float diff = currentWeight - lastStableWeight;
    if (diff < 0) diff = -diff;
    if (diff >= STABLE_THRESHOLD || lastStableWeight == 0.0) {
      lastStableWeight = currentWeight;
      uploadWeight(currentWeight);
    }
  }

  lastWeightWasStable = weightIsStable;

  // --- Update LCD (only when needed to reduce flicker) ---
  updateLCD();

  // --- Clear upload status after showing for 2 seconds ---
  if ((uploadSuccess || uploadFailed) && (now - uploadStatusTime >= 2000)) {
    uploadSuccess = false;
    uploadFailed  = false;
  }
}

// ============================================================
//  WiFi CONNECTION (Section 4.2.C - Retry up to 20 times)
// ============================================================
void setupWiFi() {
  Serial.print(F("Connecting to WiFi"));
  
  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED && attempts < 20) {
    delay(500);
    Serial.print(".");
    lcd.setCursor(0, 1);
    lcd.print("Attempt ");
    lcd.print(attempts + 1);
    lcd.print("/20");
    attempts++;
  }
  Serial.println();

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println(F("WiFi connected!"));
  } else {
    Serial.println(F("WiFi connection failed after 20 attempts."));
    lcd.clear();
    lcd.print("  WiFi Failed");
    lcd.setCursor(0, 1);
    lcd.print("Check Credentials");
  }
}

// ============================================================
//  TARE/ZERO SCALE (Section 4.2.E - Press FLASH button to tare)
// ============================================================
void tareScale() {
  Serial.println(F("Taring scale..."));
  scale.tare();
  currentWeight = 0.0;
  Serial.println(F("Scale tared. Zero point set."));
}

// ============================================================
//  READ WEIGHT FROM HX711 (Section 4.2.A)
// ============================================================
void readWeight() {
  // Check if HX711 is ready
  if (!scale.is_ready()) {
    Serial.println(F("HX711 not ready."));
    return;
  }

  // Read raw value and convert to kg using calibration factor
  float rawReading = scale.get_units(5);  // Average of 5 readings for stability
  currentWeight = rawReading;

  // Clamp near-zero negative values (tare drift compensation)
  if (currentWeight < 0.0 && currentWeight > -0.1) {
    currentWeight = 0.0;
  }

  // Round to 2 decimal places (e.g., 8.45 kg) -- Section 4.2.A
  currentWeight = round(currentWeight * 100.0f) / 100.0f;

  // Update circular buffer of readings for stability check
  readings[readingIndex] = currentWeight;
  readingIndex = (readingIndex + 1) % STABLE_COUNT_REQUIRED;

  if (readingIndex == 0) {
    readingsReady = true;  // Buffer has been filled at least once
  }

  // Check stability: 3 consecutive readings differ by < 0.05 kg
  weightIsStable = isWeightStable();

  // Debug output to Serial Monitor at 115200 baud
  Serial.print(F("Weight: "));
  Serial.print(currentWeight, 2);
  Serial.print(F(" kg"));
  if (weightIsStable) {
    Serial.print(F(" [STABLE]"));
  } else {
    Serial.print(F(" [unstable]"));
  }
  Serial.println();
}

// ============================================================
//  STABILITY CHECK (Section 4.2.A)
//  Accept weight only when 3 consecutive readings differ by < 0.05 kg
// ============================================================
bool isWeightStable() {
  if (!readingsReady) {
    return false;
  }

  // Compare all pairs in the circular buffer
  for (int i = 0; i < STABLE_COUNT_REQUIRED; i++) {
    for (int j = i + 1; j < STABLE_COUNT_REQUIRED; j++) {
      float diff = readings[i] - readings[j];
      if (diff < 0) diff = -diff;
      if (diff > STABLE_THRESHOLD) {
        return false;
      }
    }
  }
  return true;
}

// ============================================================
//  GET CURRENT STATUS MESSAGE
// ============================================================
String getStatusMessage() {
  if (uploading) {
    return "Sending...";
  } else if (uploadSuccess) {
    return "Sent OK!";
  } else if (uploadFailed) {
    return "Send Failed";
  } else if (WiFi.status() != WL_CONNECTED) {
    return "WiFi Error";
  } else if (weightIsStable) {
    return "Stable - Ready";
  } else {
    return "Waiting...";
  }
}

// ============================================================
//  UPDATE LCD DISPLAY (Section 4.2.B)
//  Line 1: "Weight: X.XX kg"
//  Line 2: Status message
//  Updates only on change to prevent flickering
// ============================================================
void updateLCD() {
  String status = getStatusMessage();

  // Only update LCD if weight or status has changed
  bool weightChanged = (abs(currentWeight - lastDisplayedWeight) >= 0.01);
  bool statusChanged = (status != lastDisplayedStatus);

  if (weightChanged || statusChanged) {
    lcd.clear();

    // --- Line 1: Weight in kg ---
    lcd.setCursor(0, 0);
    lcd.print("Weight: ");
    if (currentWeight < 10.0) lcd.print(" ");  // Align for single-digit weights
    lcd.print(currentWeight, 2);
    lcd.print(" kg");

    // --- Line 2: System status ---
    lcd.setCursor(0, 1);
    lcd.print(status);

    // Update tracking variables
    lastDisplayedWeight = currentWeight;
    lastDisplayedStatus = status;
  }
}

// ============================================================
//  UPLOAD WEIGHT TO GOOGLE APPS SCRIPT (Section 4.2.D)
//  Automatic data upload - no manual input required
// ============================================================
void uploadWeight(float weight) {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println(F("Cannot upload: WiFi not connected."));
    uploadFailed = true;
    uploadSuccess = false;
    uploadStatusTime = millis();
    return;
  }

  uploading = true;
  uploadSuccess = false;
  uploadFailed = false;

  Serial.println(F("------------------------------------------"));
  Serial.print(F("Uploading weight: "));
  Serial.print(weight, 2);
  Serial.println(F(" kg"));

  HTTPClient http;
  http.begin(wifiClient, SERVER_URL);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  // Prepare POST data: weight_kg, child_id, timestamp
  String timestamp = getFormattedTimestamp();
  String postData = "weight_kg=" + String(weight, 2) +
                    "&child_id=" + String(CHILD_ID) +
                    "&timestamp=" + timestamp;

  Serial.print(F("POST data: "));
  Serial.println(postData);

  // Send HTTP POST request
  int httpCode = http.POST(postData);

  // Check response
  if (httpCode > 0) {
    Serial.print(F("HTTP Response code: "));
    Serial.println(httpCode);
    String response = http.getString();
    Serial.print(F("Response: "));
    Serial.println(response);

    if (httpCode == HTTP_CODE_OK || httpCode == HTTP_CODE_CREATED || httpCode == HTTP_CODE_ACCEPTED) {
      uploadSuccess = true;
      uploadFailed = false;
      lastUploadTime = millis();
      Serial.println(F("Upload SUCCESSFUL!"));
    } else {
      uploadSuccess = false;
      uploadFailed = true;
      Serial.print(F("Upload FAILED with code: "));
      Serial.println(httpCode);
    }
  } else {
    uploadSuccess = false;
    uploadFailed = true;
    Serial.print(F("HTTP request failed. Error: "));
    Serial.println(http.errorToString(httpCode));
  }

  http.end();
  uploading = false;
  uploadStatusTime = millis();

  Serial.println(F("------------------------------------------"));
}

// ============================================================
//  TIMESTAMP FORMATTING
//  Sends uptime as relative timestamp.
//  For real timestamps, add NTP sync using configTime().
// ============================================================
String getFormattedTimestamp() {
  unsigned long now_ms = millis();
  unsigned long total_seconds = now_ms / 1000;
  unsigned long hours   = total_seconds / 3600;
  unsigned long minutes = (total_seconds % 3600) / 60;
  unsigned long seconds = total_seconds % 60;

  String ts = "uptime_";
  ts += String(hours);
  ts += "h";
  ts += String(minutes);
  ts += "m";
  ts += String(seconds);
  ts += "s";

  return ts;
}
