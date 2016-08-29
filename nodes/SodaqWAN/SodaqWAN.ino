#include "Hal.h"

#include "Devices.h"

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
  HalImpl.Update();
  HalImpl.CheckAndAct();
  
  delay(10);
}

