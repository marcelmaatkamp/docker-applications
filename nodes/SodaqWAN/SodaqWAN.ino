#include "Hal.h"
#define SODAQ_ONE
//#define SODAQ_MBILI

#include "Devices.h"

// Some complete random hex
uint8_t testPayload[] = { 0x53, 0x4F, 0x44, 0x41, 0x51 };

void setup()
{
  debugSerial.begin(115200);
  delay(3000);
  while ((!debugSerial) && (millis() < 10000)) {
    // Wait 10 seconds for the debugSerial
  }

  debugSerial.println("Serial connection active");

  HalImpl.setDiag(debugSerial);
  HalImpl.initHal();
}

void loop()
{
  HalImpl.sendMessage(testPayload, sizeof(testPayload));
  delay(10000);
}

