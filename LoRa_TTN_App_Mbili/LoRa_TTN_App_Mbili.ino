/*******************************************************************************
  LoRa test application
*******************************************************************************/

#include <Sodaq_RN2483.h>
#include <DHT.h>
#include "Flasher.h"
#include "FlasherOnce.h"
#include "Toggler.h"
//#include "Toucher.h"
#include "Switch.h"


/*******************************************************************************
   Definitions
*******************************************************************************/
// MBili
#define debugSerial Serial
#define loraSerial Serial1

#define NO_PARAM -1

//###########################################################
//Theos DEFs
//###########################################################
#define PROGRAM_NAME_VERSION "LoRa Theo TTN Application 0.92"
//###########################################################

#define JOINED_FLAG 0x0001 // bit 0 of statusflags

#define LAND && // Logical AND
#define BAND &  // Bitwise AND
#define LOR ||  // Logical OR
#define BOR |   // Bitwise OR

#define MICROSWITCH_PIN 20 //Use digital pin 20 for the Microswitch sensor , Touched = LOW
#define BUTTON_PIN 20 //Use digital pin 20 for the Button sensor , Touched = LOW
#define LED_PIN2 4     // Use digital pin 21 for the Reveive Led
#define LED_PIN  21    // Use digital pin 4  for the Send Led
#define DELAY_TIME_1_SEC    1000  // 1 sec delay
#define DELAY_TIME_200_MSEC 200   // 200 msec delay
#define TWO_SECONDS 2000
#define ALIVE_MESSAGE "416C69766521" // "Alive!"
#define ALIVE_FREQUENCY 30000        // 30 seconds

#define DHTPIN 5 // pin  DHT22 is connected to
#define DHTTYPE DHT22

int buttonState = 0;
bool touchSensorStatusFlag = false;
//bool doneOnce = false;

int ledPin2State = LOW;

uint8_t  rx_buffer[32] = {0}; // all elements 0
uint16_t rx_buf_len = 0;

int switchState = 0;
int switchOldState = 0;


const uint8_t devAddr[4] =
{
	0x29, 0xB1, 0xA8, 0xA0
};

// USE YOUR OWN KEYS!
const uint8_t appSKey[16] =
{ 0x85, 0xC9, 0x7E, 0xE9, 0xE1, 0x21, 0xF5, 0xEB, 0xED, 0x29, 0x7B, 0x92, 0xD1, 0x45, 0x21, 0x12};

// USE YOUR OWN KEYS!
const uint8_t nwkSKey[16] =
{ 0x0E, 0x7A, 0x96, 0x5B, 0xCA, 0x3E, 0xB1, 0x8A, 0x45, 0x6A, 0x59, 0xD7, 0xA4, 0xD9, 0x41, 0xBD };

/*******************************************************************************
   Function prototypes
*******************************************************************************/
//void sendCommandToRN2483(String Command, int Parameter);
void sendCommandToRN2483(String Command, int Parameter, bool Debug);
void sendCommandToRN2483_returnResponse(String Command, int Parameter, char* Response);
uint8_t sendMessage2(const char message[], const int length);
class Toggler;
void toggleReceiveLed();
String readDhtTemperature();



DHT dht(DHTPIN, DHTTYPE);

Switch microSwitch1(MICROSWITCH_PIN);     // Microswitch to detect case open/closed 
Flasher      ledMBili(LED1, 100, TWO_SECONDS); // RED led on MBili board; 100msec on, 2000msec off
FlasherOnce  sendLed(LED_PIN, 500);            // send led connected to MBili board; 500msec on
//Toucher      touch1(BUTTON_PIN, 100);          // Touch sensor 
Toggler      receiveLed(LED_PIN2);             // receive Led 

/*******************************************************************************
   AliveMessage class - Used to send an Alive message.
*******************************************************************************/
class AliveMessage
{
  // Class Member Variables
  // These are initialized at startup
  unsigned long AliveFrequencyMillis;     // period of time (msecs) between transmission of 2 alive messages

  // These maintain the current state
  unsigned long previousMillis;   // will store last time an Alive message was send
  char *aliveTxMessage;           // Pointer to Alive message (in HEX format)
  int messageLength;              // Length of Alive message (number of HEX chars)
  
  public:
  // Constructor - creates a AliveMessage
  // and initializes the member variables and state
  AliveMessage(long AliveFrequencyMillisValue, char *message, int messageLengthValue)
  {
    AliveFrequencyMillis = AliveFrequencyMillisValue;
    aliveTxMessage = message;
    messageLength = messageLengthValue;
    previousMillis = millis();
  }

  void Update()
  {
    // check to see if it's time to change the state of the LED
    unsigned long currentMillis = millis();
     
    if(currentMillis - previousMillis >= AliveFrequencyMillis)
    {
		previousMillis = currentMillis;  // Remember the time
		debugSerial.println("Attempt to send alive message");

		char Data_Tx[21] = "416C697665323B2020"; // 18 lang + '\0' maakt 19, dus 19 lang reserveren
                        // 012345678901234567
                        // 123456789012345678
               // wordt:   A l i v e 2 ; x x  // xy = Temperature (2 digits, whole degrees)
		String data = readDhtTemperature();
		debugSerial.print("Temp reading = ");
		debugSerial.println(data);
		if (data.substring(0,3).equals("NAN") ){
			Data_Tx[14] = '4';  // ER staat voor ERROR
			Data_Tx[15] = '5';
			Data_Tx[16] = '5';
			Data_Tx[17] = '2';
		} else {
			const char *c1;
			c1 = data.c_str();
			Data_Tx[14] = '3';  // x
			Data_Tx[15] = c1[0];
			Data_Tx[16] = '3';  // y
			Data_Tx[17] = c1[1];
		}
 
       uint8_t resultCode = 0; // NoError = 0, NoResponse = 1, Timeout = 2, PayloadSizeError = 3, InternalError = 4, Busy = 5, NetworkFatalError = 6, NotConnected = 7, NoAcknowledgment = 8
//       resultCode = sendMessage2(aliveTxMessage, messageLength); // TX the alive message
//       resultCode = sendMessage2(c1, 4); // TX the alive message
       resultCode = sendMessage2(Data_Tx, 18); // TX the alive message

      //######### RX PART ############################################  
      memset(rx_buffer, 0, sizeof(rx_buffer) / sizeof(rx_buffer[0]));
      //clearRxBuffer(rx_buffer);
      rx_buf_len = LoRaBee.receive(rx_buffer, 32, 0);
      debugSerial.print("RX buffer length = ");
      debugSerial.print (rx_buf_len);
      if (rx_buf_len != 0){
         debugSerial.print(", Contents = " );
         for (int i = 0; i <= 12; i++) {
             debugSerial.print((char)rx_buffer[i]);
         } 
      }
      debugSerial.println();
     //debugSerial.println();
      if ((char)rx_buffer[0] == 'L') {
          debugSerial.println("Yoho, Received an L message!");
          // Flip the Receive led
          // TODO: fix the next line
          receiveLed.Toggle();
      }
       
    }
  }
};


AliveMessage aliveMessage(ALIVE_FREQUENCY, ALIVE_MESSAGE, sizeof(ALIVE_MESSAGE));         

/*******************************************************************************
   Function:    void setup(void)

   Headers:     -

   Description: Setup routine

   Parameters:  -

   Return:      -
*******************************************************************************/
void setup()
{
  debugSerial.begin(57600);
  loraSerial.begin(LoRaBee.getDefaultBaudRate());
  debugSerial.println(PROGRAM_NAME_VERSION);
  debugSerial.println("");
  dumpLoRaBeeAttributesToSerial();

   pinMode(LED_PIN2, OUTPUT);
   digitalWrite(LED_PIN2, LOW); 
 
  // Check status flags to see if already joined.
//  if ((getMacStatus(true) BAND JOINED_FLAG) != 1) { // not joined yet

     if (LoRaBee.initABP(loraSerial, devAddr, appSKey, nwkSKey, true))
     {
       debugSerial.println("Connection to the network was successful.");
     }
     else
     {
       debugSerial.println("Connection to the network failed!");
     }
//  }
  getMacStatus(true);  // Blijkbaar komt de "Accepted" pas later (paar seconden), dus nog even op wachten ?
}

/*******************************************************************************
   Function:    void loop()
   Headers:     -
   Description: Main loop routine, transfers data between the serial ports of
                the Microchip LoRa module and the (debug)terminal
   Parameters:  -
   Return:      -
*******************************************************************************/
void loop()
{
  // Update status of Leds and Sensors
  sendLed.Update();
  ledMBili.Update();
  microSwitch1.Update();
  aliveMessage.Update();
  
  // Send a Lora message when sensor Button is Touched
  
  //Read the state of the microSwitch
  switchState = microSwitch1.ReadState();
  if (switchState != switchOldState) {
	switchOldState = switchState;
    Serial.print("Switch state: ");
    Serial.println(switchState);

     debugSerial.println("Send message with Switch state! ");
     uint8_t resultCode = 0; // NoError = 0, NoResponse = 1, Timeout = 2, PayloadSizeError = 3, InternalError = 4, Busy = 5, NetworkFatalError = 6, NotConnected = 7, NoAcknowledgment = 8

     //Construct data
      char Data_Tx[21] = "53656E736F72323B20"; // 18 lang + '\0' maakt 19, dus 19 lang reserveren
                       // 012345678901234567
                       // 123456789012345678
              // wordt:   S e n s o r 2 ; 1  // koffer open
              //    of:   S e n s o r 2 ; 0  // koffer dicht
      if (switchState == 0) {
        Data_Tx[16] = '3'; // status = 1 = open
        Data_Tx[17] = '1';
      } else {
        Data_Tx[16] = '3'; // status = 0 = dicht
        Data_Tx[17] = '0';
      }
     resultCode = sendMessage2(Data_Tx, 18); // "Sensor2;x"
     if (resultCode == 0) {
        sendLed.Flash();
     }
	//     touch1.Reset();

    // Copies the latest received packet (optionally starting from the "payloadStartPosition" 
    // position of the payload) into the given "buffer", up to "size" number of bytes.
    // Returns the number of bytes written or 0 if no packet is received since last transmission.
//    uint8_t rx_buffer[32] = {0}; // all elements 0

    //######### RX PART ############################################  
    memset(rx_buffer, 0, sizeof(rx_buffer) / sizeof(rx_buffer[0]));
    //clearRxBuffer(rx_buffer);
    rx_buf_len = LoRaBee.receive(rx_buffer, 32, 0);
    debugSerial.print("RX buffer length = ");
    debugSerial.print (rx_buf_len);
    if (rx_buf_len != 0){
       debugSerial.print(", Contents = " );
       for (int i = 0; i <= 12; i++) {
           debugSerial.print((char)rx_buffer[i]);
       } 
    }
    debugSerial.println();
   //debugSerial.println();
    if ((char)rx_buffer[0] == 'L') {
        debugSerial.println("Yoho, Received an L message!");
        // Flip the Receive led
        receiveLed.Toggle();
    }
	//}
  }
}


/*******************************************************************************
 Function:    sendMessage2(const char message[])
 Headers:     -
 Description: Send a message to the GW
 Parameters:  message - in - HEX string with message to send to GateWay
              length  - in - length of HEX string with message
 Return:      uint8_t - result as recived from LoRaBee.send()
 *******************************************************************************/
uint8_t sendMessage2(const char message[], const int length) {

    debugSerial.print("Frame counter next uplink TX: ");
    sendCommandToRN2483("mac get upctr", NO_PARAM, true);
    debugSerial.println("");
    
    debugSerial.println("Send message");
    uint8_t testPayload[length/2];
    convertHexStringToByteArray(message, testPayload, true);  
    uint8_t resultCode = 0xFF; // NoError = 0, NoResponse = 1, Timeout = 2, PayloadSizeError = 3, InternalError = 4, Busy = 5, NetworkFatalError = 6, NotConnected = 7, NoAcknowledgment = 8
    int retryCounter = 1;
    while( (resultCode != NoError) && (retryCounter < 4) ){
        resultCode = LoRaBee.send(loraSerial, testPayload, length/2);
        //resultCode = LoRaBee.sendReqAck(loraSerial, testPayload, length/2, 3); // 3 retries
        //resultCode = LoRaBee.sendReqAck(loraSerial, testPayload, 11, 3);
        // LoRaBee.send definition = uint8_t send(uint8_t port, const uint8_t* payload, uint8_t size);
        debugSerial.print("Attempt = ");
        debugSerial.print(retryCounter);
        debugSerial.print(", Resultcode = ");
        debugSerial.print(resultCode, DEC);
        debugSerial.print("      ");
        retryCounter++;
        delay(1000);
    }  
    if (resultCode != NoError)
    {
        debugSerial.println("Send the packet: FAILED after 3 retries!");
    } else {
        debugSerial.println("Send the packet: SUCCESS!");
    }
    return resultCode;
}



/*******************************************************************************
 Function:    sendMessage(bool OnOff)
 Headers:     -
 Description: Send a message to the GW
 Parameters:  On      - in  boolean, function sends an ON message (if true) or an OFF message otherwise
 Return:      uint8_t - result as received from LoRaBee.send()
 *******************************************************************************/
uint8_t sendMessage(bool On) {
  
  uint8_t testPayload[11];
  uint8_t resultCode = 0; // NoError = 0, NoResponse = 1, Timeout = 2, PayloadSizeError = 3, InternalError = 4, Busy = 5, NetworkFatalError = 6, NotConnected = 7, NoAcknowledgment = 8
  if (On) {
     debugSerial.println("Send 'Led is ON!'");
//     convertHexStringToByteArray("4C6564206973204F4E2120", testPayload, true); // "Led is ON! "  
  } else {
     debugSerial.println("Send 'Led is OFF!'");
//     convertHexStringToByteArray("4C6564206973204F464621", testPayload, true); // "Led is OFF!" 
  }
  //resultCode = LoRaBee.sendReqAck(loraSerial, testPayload, 11, 3);
  resultCode = LoRaBee.send(loraSerial, testPayload, 11);
  debugSerial.print("Resultcode = ");
  debugSerial.print (resultCode, DEC);
  debugSerial.println();
  if (resultCode != NoError)
  {
      debugSerial.println("Send the packet: FAILED!");
  } else {
      debugSerial.println("Send the packet: SUCCESS!");
  }
  return resultCode;
}



/*******************************************************************************
   Function:    convertHexStringToByteArray(const char hexStr[], uint8_t byteArray[])
   Headers:     -
   Description: Converts a Hex character string to an array of bytes
   Parameters:  HexStr    - in  - String with Hex chars (Must be all in upper case!!)
                ByteArray - out - Storage to hold the conversion result
                Debug     - in  - Send debug info to terminal when true
   Return:      -
*******************************************************************************/
void convertHexStringToByteArray(const char* HexStr, uint8_t ByteArray[], bool Debug ) {

  for (int i = 0; i < strlen(HexStr); i += 2)
  {
    if (Debug) {
      debugSerial.write(HexStr[i]);
      debugSerial.write(HexStr[i+1]);
    }
    ByteArray[i/2] = HEX_PAIR_TO_BYTE(HexStr[i], HexStr[i+1]);
  }
  if (Debug) {
      debugSerial.println();
  }
}



/*******************************************************************************
   Function:    sendCommandToRN2483(String Command, int Parameter, bool Debug)
   Headers:     -
   Description: Sends command to RN2483
                Optional prints result to serial debug port with suppression of /n
   Parameters:  Command   - in  - Command to send to RF module
                Parameter - in  - Optional parameter too add to the Command (value -1 means no parameters to add)
                Debug     - in  - Send debug info to terminal when true
   Return:      -
*******************************************************************************/
void sendCommandToRN2483(String Command, int Parameter, bool Debug) {

  char ReadByte = 0;

  // Send command + parameter
  loraSerial.print(Command);
  if (Parameter >= 0)
    loraSerial.println(Parameter, DEC);
  else
    loraSerial.println("");
  // Print response
  while (ReadByte != '\r')
  {
    while (!loraSerial.available()) {} //wait for the first character in buffer
    ReadByte = loraSerial.read();
    if (ReadByte != '\r' && ReadByte != '\n') {
      if (Debug) {
        debugSerial.write(ReadByte);
      }
    }
  }
//  if (Debug) {
//     debugSerial.print("\r\n");
//  }
}


/*******************************************************************************
   Function:    sendCommandToRN2483_returnResponse(String Command, int Parameter, char* Response, bool Debug)
   Headers:     -
   Description: Sends command to RN2483
                Optional print result to serial debug port with suppression of /n
                Returns the response of the RF module
   Parameters:  Command   - in  - Command to send to RF module
                Parameter - in  - Optional parameter too add to the Command (value -1 means no parameters to add)
                Response  - out - Sting array to hold the response from the RF miodule
                Debug     - in  - Send debug info to terminal when true
   Return:      -
*******************************************************************************/
void sendCommandToRN2483_returnResponse(String Command, int Parameter, char* Response) {

  char ReadByte = 0;

  // Send command + parameter
  loraSerial.print(Command);
  if (Parameter >= 0)
    loraSerial.println(Parameter, DEC);
  else
    loraSerial.println("");
  // Print response
  while (ReadByte != '\r')
  {
    while (!loraSerial.available()) { //wait for the first character in buffer
    } 
    ReadByte = loraSerial.read();
    if (ReadByte != '\r' && ReadByte != '\n')

      //debugSerial.write(ReadByte);
      if (Response != NULL) {
          *Response = ReadByte;
          Response++;
          *Response = '\0';
      }
  }
}

/*******************************************************************************
   Function:    getMacStatus(bool Debug)
   Headers:     -
   Description: Gets the status of the RF module
   Parameters:  Debug - in  - Send debug info to terminal when true
   Return:      uint16_t - Status of the RF module (16 bits), See command reference for details
*******************************************************************************/
uint16_t getMacStatus(bool Debug) {

  char Status_response[5] = "";
  sendCommandToRN2483_returnResponse("mac get status", NO_PARAM, Status_response);
 // uint16_t status = atoi(Status_response);
    uint16_t status = (int)strtoul(Status_response, NULL, 16);
 // int number = (int)strtol(hexstring, NULL, 16);
  if (Debug) {
    debugSerial.print ("Status (string) = ");
    debugSerial.print (Status_response);
    debugSerial.print (", Status(int) = ");
    debugSerial.print (status);
    if (status BAND JOINED_FLAG) {
      debugSerial.println (", Joined!");
    }
    else
    {
      debugSerial.println (", NOT Joined!");
    }
  }
  return status;
}

/*******************************************************************************
   Function:    dumpLoRaBeeAttributesToSerial()
   Headers:     -
   Description: Reads lot of attibutes and settings from the LoRaBee module and dump them to the Serial output
   Parameters:  -
   Return:      -
*******************************************************************************/
void dumpLoRaBeeAttributesToSerial() {

  debugSerial.println("Microchip RN2483 parameters:");
  debugSerial.print("Firmware versie                                \t");
  sendCommandToRN2483("sys get ver", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("Hardware EUI                                   \t");
  sendCommandToRN2483("sys get hweui", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("DevEUI                                         \t");
  sendCommandToRN2483("mac get deveui", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("AppEUI                                         \t");
  sendCommandToRN2483("mac get appeui", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("DevAddr                                        \t");
  sendCommandToRN2483("mac get devaddr", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("Radio band                                     \t");
  sendCommandToRN2483("mac get band", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("Output Power [1-5] (default 1 = max 14dBm)     \t");
  sendCommandToRN2483("mac get pwridx", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("ADR status                                     \t");
  sendCommandToRN2483("mac get adr", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("Max retransmissions [0-255] (default 7)       \t ");
  sendCommandToRN2483("mac get retx", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("Hex sync word (default 34 for public networks) \t");
  sendCommandToRN2483("mac get sync", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("Auto reply to DL ack req or pending DL mess    \t");
  sendCommandToRN2483("mac get ar", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("Data rate [0-7] (0=SF12) + freq 2nd RX slot    \t");
  sendCommandToRN2483("mac get rx2 868", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("Delay TX - RX window 1 (default 1000) [ms]     \t");
  sendCommandToRN2483("mac get rxdelay1", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("Delay TX - RX window 2 (default 2000) [ms]     \t");
  sendCommandToRN2483("mac get rxdelay2", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("TX duty cycle prescaler (all channels)         \t");
  sendCommandToRN2483("mac get dcycleps", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("Status flags                                   \t");
  sendCommandToRN2483("mac get status", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("Data rate next TX [0-7] (default 5 = SF7)      \t");
  sendCommandToRN2483("mac get dr", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("Gateways that recv the last Link Check Req     \t");
  sendCommandToRN2483("mac get gwnb", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("Demodulation margin of last Link Check Req [dB]\t");
  sendCommandToRN2483("mac get mrgn", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("Frame counter next uplink TX                   \t");
  sendCommandToRN2483("mac get upctr", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("Frame counter next downlink RX                 \t");
  sendCommandToRN2483("mac get dwnctr", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("SNR last received packet [-128 to +127]        \t");
  sendCommandToRN2483("radio get snr", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.print("Supply voltage [mV]                            \t");
  sendCommandToRN2483("sys get vdd", NO_PARAM, true);
  debugSerial.println("");
  debugSerial.println("");
  debugSerial.print("\t\t\t\t\t\tfreq\t\tstat\tmin-max dr\tduty cycle\r\n");
  for (int i = 0; i < 16; i++) {
    debugSerial.print("Radio channel ");
    debugSerial.print (i, DEC);
    if (i < 10) {
      debugSerial.print("\t\t\t\t\t");
    }
    else {
      debugSerial.print("\t\t\t\t");
    }
    sendCommandToRN2483("mac get ch freq ", i, true);
    debugSerial.print("\t");
    sendCommandToRN2483("mac get ch status ", i, true);
    debugSerial.print("\t");
    sendCommandToRN2483("mac get ch drrange ", i, true);
    debugSerial.print("\t\t");
    sendCommandToRN2483("mac get ch dcycle ", i, true);
    debugSerial.print("\r\n");
  }

}

String readDhtTemperature()
{
	// Reading temperature or humidity takes about 250 milliseconds!
	// Sensor readings may also be up to A0 seconds 'old' (its a very slow sensor)
	float t = dht.readTemperature();
	String data;
	if (isnan(t)) {
	data = "NAN";
	} else {
	data = String(t);
	} 
	return data;
}
