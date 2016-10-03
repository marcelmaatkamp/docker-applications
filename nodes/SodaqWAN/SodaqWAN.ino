#include "Hal.h"

#include "Devices.h"

void setup()
{
  debugSerial.begin(115200);
  debugSerial.println("Serial connection active");
  debugSerial.print("Starting ");
  debugSerial.print(NODE_TXT);
  debugSerial.print(" node on network ");
  debugSerial.println(NETWORK_TXT);

  delay(3000);
  
  HalImpl.setDiag(debugSerial);
  HalImpl.initHal();
}

void loop()
{
  HalImpl.Update();
  HalImpl.CheckAndAct();
  
  delay(10);
}

