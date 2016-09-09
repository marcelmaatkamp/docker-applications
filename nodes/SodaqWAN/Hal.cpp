#define DEBUG

#include "Devices.h"
#include "Hal.h"
#include "Alive.h"
#include "Switch.h"
#include "Voltage.h"
#include "Temperature.h"

Hal HalImpl;
Switch microSwitch(MICROSWITCH_PIN);
Temperature tempSensor(DHTPIN);
Voltage voltage(VOLT_PIN);
Alive alive(ALIVE_INTERVAL * 1000);

Hal::Hal()
{
}

// Initialize the Hal and all the stuff in it
void Hal::init()
{
}

#ifdef SODAQ_ONE
void RED() {
  digitalWrite(LED_RED, LOW);
  digitalWrite(LED_GREEN, HIGH);
  digitalWrite(LED_BLUE, HIGH);
}

void GREEN() {
  digitalWrite(LED_RED, HIGH);
  digitalWrite(LED_GREEN, LOW);
  digitalWrite(LED_BLUE, HIGH);
}

void BLUE() {
  digitalWrite(LED_RED, HIGH);
  digitalWrite(LED_GREEN, HIGH);
  digitalWrite(LED_BLUE, LOW);
}
#endif

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

bool switchsensor_callback(pb_ostream_t *stream, const pb_field_t *field, void * const *arg)
{
//  debugPrintLn("In Switch callback");

  SensorReading sensormsg = SensorReading_init_zero;

  /* Fill in the lucky number */
  sensormsg.id = 1;
  sensormsg.has_id = true;
  sensormsg.value1 = microSwitch.ReadState();
  sensormsg.has_value1 = true;

  /* This encodes the header for the field, based on the constant info
     from pb_field_t. */
  if (!pb_encode_tag_for_field(stream, field))
    return false;

  /* This encodes the data for the field, based on our FileInfo structure. */
  if (!pb_encode_submessage(stream, SensorReading_fields, &sensormsg))
    return false;

  return true;
}

bool tempsensor_callback(pb_ostream_t *stream, const pb_field_t *field, void * const *arg)
{
//  debugPrintLn("In Temp callback");

  SensorReading sensormsg = SensorReading_init_zero;

  /* Fill in the lucky number */
  sensormsg.id = 2;
  sensormsg.has_id = true;
  if (tempSensor.isValid())
  {
    sensormsg.value1 = tempSensor.getTemp();
    sensormsg.has_value1 = true;
//    debugPrint("The temperature is:");
//    debugPrintLn(tempSensor.getTemp());
  }
  else
  {
    sensormsg.error = 1;
    sensormsg.has_error = true;
//    debugPrintLn("Temp gifs NAN");
  }

  /* This encodes the header for the field, based on the constant info
     from pb_field_t. */
  if (!pb_encode_tag_for_field(stream, field))
    return false;

  /* This encodes the data for the field, based on our FileInfo structure. */
  if (!pb_encode_submessage(stream, SensorReading_fields, &sensormsg))
    return false;

  return true;
}

bool humsensor_callback(pb_ostream_t *stream, const pb_field_t *field, void * const *arg)
{
//  debugPrintLn("In Humidity callback");

  SensorReading sensormsg = SensorReading_init_zero;

  /* Fill in the lucky number */
  sensormsg.id = 3;
  sensormsg.has_id = true;
  if (tempSensor.isValid())
  {
    sensormsg.value1 = tempSensor.getHumidity();
    sensormsg.has_value1 = true;
//    debugPrint("The humidity is:");
//    debugPrintLn(tempSensor.getHumidity());
  }
  else
  {
    sensormsg.error = 1;
    sensormsg.has_error = true;
//    debugPrintLn("Humidity gifs NAN");
  }

  /* This encodes the header for the field, based on the constant info
     from pb_field_t. */
  if (!pb_encode_tag_for_field(stream, field))
    return false;

  /* This encodes the data for the field, based on our FileInfo structure. */
  if (!pb_encode_submessage(stream, SensorReading_fields, &sensormsg))
    return false;

  return true;
}

bool voltsensor_callback(pb_ostream_t *stream, const pb_field_t *field, void * const *arg)
{
//  debugPrintLn("In Voltage callback");

  SensorReading sensormsg = SensorReading_init_zero;

  /* Fill in the lucky number */
  sensormsg.id = 4;
  sensormsg.has_id = true;
  if (voltage.isValid())
  {
    sensormsg.value1 = voltage.getValue();
    sensormsg.has_value1 = true;
//    debugPrint("The voltage is:");
//    debugPrintLn(voltage.getValue());
  }
  else
  {
    sensormsg.error = 1;
    sensormsg.has_error = true;
//    debugPrintLn("The voltage gifs an Error");
  }

  /* This encodes the header for the field, based on the constant info
     from pb_field_t. */
  if (!pb_encode_tag_for_field(stream, field))
    return false;

  /* This encodes the data for the field, based on our FileInfo structure. */
  if (!pb_encode_submessage(stream, SensorReading_fields, &sensormsg))
    return false;

  return true;
}

bool Hal::CheckAndAct()
{
  //Read the state of the microSwitch
  if (microSwitch.isChanged()) {
    debugPrint("Switch state: ");
    debugPrintLn(microSwitch.ReadState());

    uint8_t buf[128];
    size_t message_length;

    NodeMessage nodemsg = NodeMessage_init_zero;

    /* Create a stream that will write to our buffer. */
    pb_ostream_t stream = pb_ostream_from_buffer(buf, sizeof(buf));

    nodemsg.reading.funcs.encode = &switchsensor_callback;

    /* Now we are ready to encode the message! */
    /* Then just check for any errors.. */
    if (!pb_encode(&stream, NodeMessage_fields, &nodemsg))
    {
      debugPrint("Encoding failed: ");
      debugPrintLn(PB_GET_ERROR(&stream));
    }
    else
    {
      message_length = stream.bytes_written;
      debugPrint("message_length:");
      debugPrintLn(message_length);
      debugPrint("message:<");
      for (uint8_t i = 0; i < message_length; i++)
      {
        debugPrint(buf[i], HEX);
        if (i < message_length - 1)
          debugPrint(" ");
      }
      debugPrintLn(">");
    }
    HalImpl.sendMessage(buf, message_length);
  }

  // check alive timer
  if (alive.isTimePassed())
  {
    // reset the Time Passed flag
    alive.resetTimePassed();

    //    // Read the value from the temp sensor, that is temp and humidity
    //    String data = tempSensor.getData();
    //    // Read the value from the voltage sensor
    //    String volt = voltage.getData();
    //    // print the values for debug
    //    debugPrintLn(data + ";" + volt);
    //
    //    // make the message, fill the default
    //    uint8_t alivePayload[] = { "Alive3;00.00;00.00;00.00" }; // temp;hum;voltage
    //    char charArr[12];
    //    // process the temperature and humidity
    //    if (data.substring(0, 3) != "NAN" )
    //    {
    //      data.toCharArray(charArr, 12);
    //      for (int i = 0; i < 12; i++)
    //      {
    //        alivePayload[i + 7] = (uint8_t)charArr[i];
    //      }
    //      alivePayload[18] = ';';
    //    }
    //    // process the voltage
    //    volt.toCharArray(charArr, 5);
    //    for (int i = 0; i < 4; i++)
    //    {
    //      alivePayload[i + 20] = (uint8_t)charArr[i];
    //    }
    //    // send the message
    //    HalImpl.sendMessage(alivePayload, sizeof(alivePayload) - 1);

    uint8_t buf2[128];
    size_t message_length;

    NodeMessage nodemsg2 = NodeMessage_init_zero;

    /* Create a stream that will write to our buffer. */
    pb_ostream_t stream2 = pb_ostream_from_buffer(buf2, sizeof(buf2));

    // add the temperature data to the output buffer 
    nodemsg2.reading.funcs.encode = &tempsensor_callback;

    /* Now we are ready to encode the message! */
    /* Then just check for any errors.. */
    if (!pb_encode(&stream2, NodeMessage_fields, &nodemsg2))
    {
      debugPrint("Encoding failed: ");
      debugPrintLn(PB_GET_ERROR(&stream2));
    }
    else
    {
      message_length = stream2.bytes_written;
      debugPrint("message_length:");
      debugPrintLn(message_length);
      debugPrint("message:<");
      for (uint8_t i = 0; i < message_length; i++)
      {
        debugPrint(buf2[i], HEX);
        if (i < message_length - 1)
          debugPrint(" ");
      }
      debugPrintLn(">");
    }

    // add the humidity data to the output buffer 
    nodemsg2.reading.funcs.encode = &humsensor_callback;

    /* Now we are ready to encode the message! */
    /* Then just check for any errors.. */
    if (!pb_encode(&stream2, NodeMessage_fields, &nodemsg2))
    {
      debugPrint("Encoding failed: ");
      debugPrintLn(PB_GET_ERROR(&stream2));
    }
    else
    {
      message_length = stream2.bytes_written;
      debugPrint("message_length:");
      debugPrintLn(message_length);
      debugPrint("message:<");
      for (uint8_t i = 0; i < message_length; i++)
      {
        debugPrint(buf2[i], HEX);
        if (i < message_length - 1)
          debugPrint(" ");
      }
      debugPrintLn(">");
    }

    // add the voltage data to the output buffer 
    nodemsg2.reading.funcs.encode = &voltsensor_callback;

    /* Now we are ready to encode the message! */
    /* Then just check for any errors.. */
    if (!pb_encode(&stream2, NodeMessage_fields, &nodemsg2))
    {
      debugPrint("Encoding failed: ");
      debugPrintLn(PB_GET_ERROR(&stream2));
    }
    else
    {
      message_length = stream2.bytes_written;
      debugPrint("message_length:");
      debugPrintLn(message_length);
      debugPrint("message:<");
      for (uint8_t i = 0; i < message_length; i++)
      {
        debugPrint(buf2[i], HEX);
        if (i < message_length - 1)
          debugPrint(" ");
      }
      debugPrintLn(">");
    }

    HalImpl.sendMessage(buf2, message_length);
  }
}

// initialize the Lora stack
bool Hal::initLora()
{
#ifdef SODAQ_ONE
  // enable power, only for the Sodaq One
  //  pinMode(ENABLE_PIN_IO, OUTPUT);
  //  digitalWrite(ENABLE_PIN_IO, HIGH);
  // enable power to the grove shield
  pinMode(11, OUTPUT);
  digitalWrite(11, HIGH);

  pinMode(LED_RED, OUTPUT);
  pinMode(LED_GREEN, OUTPUT);
  pinMode(LED_BLUE, OUTPUT);
  GREEN();
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
  //  microSwitch.setDiag(debugSerial);
  //  microSwitch.setPin(MICROSWITCH_PIN);          // Microswitch to detect case open/closed
}

// initialize the Lora stack
bool Hal::initTemperature()
{
}

bool Hal::sendMessage(const uint8_t* payload, uint8_t size)
{
  bool retVal = true;
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
        retVal = false;
        break;
      case Busy:
        debugPrintLn("The device is busy. Sleeping for 10 extra seconds.");
        delay(10000);
        break;
      case NetworkFatalError:
        debugPrintLn("There is a non-recoverable error with the network connection. You should re-connect.\r\nThe program will now halt.");
        //        while (1) {};
        retVal = false;
        break;
      case NotConnected:
        debugPrintLn("The device is not connected to the network. Please connect to the network before attempting to send data.\r\nThe program will now halt.");
        //        while (1) {};
        retVal = false;
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


