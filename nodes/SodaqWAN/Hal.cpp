
#define DEBUG

#include "Devices.h"
#include "Hal.h"

Hal HalImpl;

Hal::Hal()
{
//  debugPrintLn("Nu in Hal::Hal()");
}

void Hal::init()
{
#ifdef SODAQ_ONE
  // enable power, only for the Sodaq One
  digitalWrite(ENABLE_PIN_IO, HIGH);
#endif  
}

bool Hal::initHal()
{
  loraSerial.begin(LoRaBee.getDefaultBaudRate());

  LoRaBee.setDiag(debugSerial); // optional
  if (LoRaBee.initABP(loraSerial, devAddr, appSKey, nwkSKey, false))
  {
    debugPrintLn("Connection to the network was successful.");
    isHalInitialized = true;
  }
  else
  {
    debugPrintLn("Connection to the network failed!");
  }
}

bool Hal::sendMessage(const uint8_t* payload, uint8_t size)
{
  bool retVal=true;
  uint8_t sendReturn;
  
  if (isInitialized()) 
  {
    if (getAcknowledge())
    {
      sendReturn = LoRaBee.sendReqAck(1, payload, size, 3);
    }
    else
    {
      sendReturn = LoRaBee.send(1, payload, size);
    }
    switch (sendReturn)
    {
      case NoError:
        debugPrintLn("Successful transmission.");
        break;
      case NoResponse:
        debugPrintLn("There was no response from the device.");
        break;
      case Timeout:
        debugPrintLn("Connection timed-out. Check your serial connection to the device! Sleeping for 20sec.");
        delay(20000);
        break;
      case PayloadSizeError:
        debugPrintLn("The size of the payload is greater than allowed. Transmission failed!");
        break;
      case InternalError:
        debugPrintLn("Oh No! This shouldn't happen. Something is really wrong! Try restarting the device!\r\nThe program will now halt.");
  //      while (1) {};
        retVal=false;
        break;
      case Busy:
        debugPrintLn("The device is busy. Sleeping for 10 extra seconds.");
        delay(10000);
        break;
      case NetworkFatalError:
        debugPrintLn("There is a non-recoverable error with the network connection. You should re-connect.\r\nThe program will now halt.");
//        while (1) {};
        retVal=false;
        break;
      case NotConnected:
        debugPrintLn("The device is not connected to the network. Please connect to the network before attempting to send data.\r\nThe program will now halt.");
//        while (1) {};
        retVal=false;
        break;
      case NoAcknowledgment:
        debugPrintLn("There was no acknowledgment sent back!");
        break;
      default:
        break;
    }
  } 
  else 
  {
      retVal = false;
  }
  return retVal;
}

