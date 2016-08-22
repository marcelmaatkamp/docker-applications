#include "Sodaq_RN2483.h"

#define debugSerial SerialUSB
#define loraSerial Serial1

// Sodaq One
const uint8_t devAddr[4] = { 0x7F, 0xEE, 0x6E, 0x5B };
const uint8_t appSKey[16] = { 0x5D, 0xAD, 0x6E, 0x20, 0xBE, 0x3E, 0x9F, 0xED, 0x94, 0x5F, 0xD0, 0xBC, 0x12, 0x31, 0xF0, 0xD5 };
const uint8_t nwkSKey[16] = { 0x8A, 0xA7, 0x64, 0xD8, 0xEB, 0x1B, 0xE3, 0x38, 0x55, 0xE0, 0x1C, 0xFF, 0x92, 0xDF, 0x43, 0xFD };

// Some complete random hex
uint8_t testPayload[] = { 0x53, 0x4F, 0x44, 0x41, 0x51 };

void setup()
{
  debugSerial.begin(115200);

  digitalWrite(ENABLE_PIN_IO, HIGH);
  delay(3000);
  while ((!debugSerial) && (millis() < 10000)) {
    // Wait 10 seconds for the debugSerial
  }

  loraSerial.begin(LoRaBee.getDefaultBaudRate());

  LoRaBee.setDiag(debugSerial); // optional
  if (LoRaBee.initABP(loraSerial, devAddr, appSKey, nwkSKey, false))
  {
    debugSerial.println("Connection to the network was successful.");
  }
  else
  {
    debugSerial.println("Connection to the network failed!");
  }

  debugSerial.println("Sleeping for 5 seconds before starting sending out test packets.");
  for (uint8_t i = 5; i > 0; i--)
  {
    debugSerial.println(i);
    delay(1000);
  }
}

void loop()
{
//  switch (LoRaBee.sendReqAck(1, testPayload, 5, 3))
  switch (LoRaBee.send(1, testPayload, 5))
  {
    case NoError:
      debugSerial.println("Successful transmission.");
      break;
    case NoResponse:
      debugSerial.println("There was no response from the device.");
      break;
    case Timeout:
      debugSerial.println("Connection timed-out. Check your serial connection to the device! Sleeping for 20sec.");
      delay(20000);
      break;
    case PayloadSizeError:
      debugSerial.println("The size of the payload is greater than allowed. Transmission failed!");
      break;
    case InternalError:
      debugSerial.println("Oh No! This shouldn't happen. Something is really wrong! Try restarting the device!\r\nThe program will now halt.");
      while (1) {};
      break;
    case Busy:
      debugSerial.println("The device is busy. Sleeping for 10 extra seconds.");
      delay(10000);
      break;
    case NetworkFatalError:
      debugSerial.println("There is a non-recoverable error with the network connection. You should re-connect.\r\nThe program will now halt.");
      while (1) {};
      break;
    case NotConnected:
      debugSerial.println("The device is not connected to the network. Please connect to the network before attempting to send data.\r\nThe program will now halt.");
      while (1) {};
      break;
    case NoAcknowledgment:
      debugSerial.println("There was no acknowledgment sent back!");
      break;
    default:
      break;
  }
  delay(10000);
}

