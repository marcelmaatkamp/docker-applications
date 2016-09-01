
#define DEBUG

#include "Devices.h"
#include "Hal.h"
#include "Alive.h"
#include "Switch.h"
#include "Voltage.h"
#include "Temperature.h"

Hal HalImpl;
Switch microSwitch(-1);
Temperature tempSensor(DHTPIN);
Voltage voltage(VOLT_PIN);
Alive alive(ALIVE_INTERVAL*1000);

Hal::Hal()
{
}

// Initialize the Hal and all the stuff in it
void Hal::init()
{
}

bool Hal::initHal()
{
  // initialize all the hardware
  initLora();
  initSwitch();
  initTemperature();
}

// Give the Hal time to do his work and check all the stuff
bool Hal::Update()
{
  microSwitch.Update();
  tempSensor.Update();
  voltage.Update();
  alive.Update();
}

bool Hal::CheckAndAct()
{
  //Read the state of the microSwitch
  switchState = microSwitch.ReadState();
  if (switchState != switchOldState) {
    switchOldState = switchState;
    debugPrint("Switch state: ");
    debugPrintLn(switchState);
    
    // Some complete random hex
    uint8_t testPayload[] = { "Sensor3;x" };
    if (switchState == 1) {
      testPayload[8] = '0';
    }
    else
    {
      testPayload[8] = '1';
    }

    HalImpl.sendMessage(testPayload, sizeof(testPayload)-1);
  }

  if (alive.isTimePassed())
  {
    // Read the value from the temp sensor, that is temp and humidity
    String data = tempSensor.getData();
    // Read the value from the voltage sensor
    String volt = voltage.getData();
    // print the values for debug
    debugPrintLn(data+";"+volt);
    // reset the Time Passed flag
    alive.resetTimePassed();

    // make the message, fill the default
    uint8_t alivePayload[] = { "Alive3;00.00;00.00;00.00" }; // temp;hum;voltage
    char charArr[12];
    // process the temperature and humidity
    if (data.substring(0,3) != "NAN" )
    {
      data.toCharArray(charArr, 12);
      for (int i=0; i < 12;i++)
      {
        alivePayload[i+7] = (uint8_t)charArr[i];
      }
      alivePayload[18] = ';';
    }
    // process the voltage
    volt.toCharArray(charArr, 5);
    for (int i=0; i < 4;i++)
    {
      alivePayload[i+20] = (uint8_t)charArr[i];
    }
    // send the message
    HalImpl.sendMessage(alivePayload, sizeof(alivePayload)-1);
  }
}

// initialize the Lora stack
bool Hal::initLora()
{
#ifdef SODAQ_ONE
  // enable power, only for the Sodaq One
  pinMode(ENABLE_PIN_IO, OUTPUT);
  digitalWrite(ENABLE_PIN_IO, HIGH);
  // enable power to the grove shield
  pinMode(11, OUTPUT);
  digitalWrite(11, HIGH);
#endif  

  loraSerial.begin(LoRaBee.getDefaultBaudRate());

  LoRaBee.setDiag(debugSerial); // optional
  if (LoRaBee.initABP(loraSerial, devAddr, appSKey, nwkSKey, false))
  {
    debugPrintLn("Connection to the network was successful.");
    isHalInitialized = true;
//    LoRaBee.resetUplinkCntr();
  }
  else
  {
    debugPrintLn("Connection to the network failed!");
  }
}

// initialize the Switch
bool Hal::initSwitch()
{
  microSwitch.setDiag(debugSerial);
  microSwitch.setPin(MICROSWITCH_PIN);          // Microswitch to detect case open/closed 
}

// initialize the Lora stack
bool Hal::initTemperature()
{
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

