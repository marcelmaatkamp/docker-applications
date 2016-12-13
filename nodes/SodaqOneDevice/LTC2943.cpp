/*!
LTC2943: Multicell Battery Gas Gauge with Temperature, Voltage and Current Measurement.
LTC2943-1: Multicell Battery Gas Gauge with Temperature, Voltage and Current Measurement.

The LTC2943 measures battery charge state, battery voltage,
battery current and its own temperature in portable
product applications. The wide input voltage range allows
use with multicell batteries up to 20V. A precision coulomb
counter integrates current through a sense resistor between
the battery’s positive terminal and the load or charger.
Voltage, current and temperature are measured with an
internal 14-bit No Latency ΔΣ™ ADC. The measurements
are stored in internal registers accessible via the onboard
I2C/SMBus Interface

http://www.linear.com/product/LTC2943
http://www.linear.com/product/LTC2943-1
*/
//#define DEBUG

#include <Stream.h>
#include <stdint.h>
#include "Config.h"
#include "LTC2943.h"

#define debugPrintln(...)  if (debugFlag==1) diagStream->println(__VA_ARGS__); 
#define debugPrint(x)    if (debugFlag==1) diagStream->print(x); 
//#ifdef DEBUG
//#define debugPrint(x) diagStream->print(x)
//#define debugPrintln(x) diagStream->println(x)
//#else
//#define debugPrint(x)
//#define debugPrintln(x)
//#endif

LTC::LTC(int pin) 
{
  setPin(pin);
  charge = 0;  // Initialize charge to an invalid value which should be ignored when processing
}

LTC::~LTC() {
}

void LTC::setPin(int pin)
{
  if (pin > -1)
  {
    inputPin = pin;
  }
}

// Sets the optional "Diagnostics and Debug" stream.
void LTC::setDiag(Stream& stream, bool dbg) 
{ 
  diagStream = &stream; 
  debugFlag = dbg;
}

int8_t LTC::Update()
{
  int8_t LTC2943_mode;
  int8_t ack = 0;
  uint16_t prescalarValue = 4096;
  uint8_t status_code, hightemp_code, lowtemp_code;
  uint16_t charge_code, current_code, voltage_code, temperature_code, chargeReading;

  // reset valid flag
  valid = true;
 
  LTC2943_mode = LTC2943_AUTOMATIC_MODE | LTC2943_PRESCALAR_M_4096 | LTC2943_ALERT_MODE ;
  ack |= LTC2943_write(LTC2943_I2C_ADDRESS, LTC2943_CONTROL_REG, LTC2943_mode);                     //! Writes the set mode to the LTC2943 control register
  ack |= LTC2943_read_16_bits(LTC2943_I2C_ADDRESS, LTC2943_ACCUM_CHARGE_MSB_REG, &charge_code);     //! Read MSB and LSB Accumulated Charge Registers for 16 bit charge code
  ack |= LTC2943_read_16_bits(LTC2943_I2C_ADDRESS, LTC2943_VOLTAGE_MSB_REG, &voltage_code);         //! Read MSB and LSB Voltage Registers for 16 bit voltage code
  ack |= LTC2943_read_16_bits(LTC2943_I2C_ADDRESS, LTC2943_CURRENT_MSB_REG, &current_code);         //! Read MSB and LSB Current Registers for 16 bit current code
  ack |= LTC2943_read_16_bits(LTC2943_I2C_ADDRESS, LTC2943_TEMPERATURE_MSB_REG, &temperature_code); //! Read MSB and LSB Temperature Registers for 16 bit temperature code
  ack |= LTC2943_read(LTC2943_I2C_ADDRESS, LTC2943_STATUS_REG, &status_code);                       //! Read Status Register for 8 bit status code

  debugPrint("Verbruik  uit LTC (register value): ");
  debugPrint(charge_code);
  debugPrint(",  ack: ");
  debugPrint(ack);
  debugPrint(",  status_code: ");
  debugPrint(status_code);
  debugPrint(",  chargeState: ");
  debugPrintln(chargeState);

  if (ack == 0) { // I2C reading ok
     chargeState = true;
     
     chargeReading = LTC2943_code_to_mAh(charge_code, resistor, prescalarValue);
     debugPrint("Verbruik  uit LTC (in mAh): ");
     debugPrintln(chargeReading);
     
     if (status_code && 1 == 1) { // UVLO bit = 1, Accu is los geweest (UVLO bit wordt automatically gereset door lezen van Status register
        debugPrintln("Case 1");

        // charge -> flash (eenmalig)
        params.setChargeOffset(charge);
        debugPrint("Verbruik van RAM naar Flash (chargeOffset in mAh): ");
        debugPrintln(charge);
     } else {
        debugPrintln("Case 2");
     }
     // Calculate new charge value (in mAh) based on an offset from flash and the charge value from the LTC (in mAh)
     charge = (float)params.getChargeOffset() + chargeReading;
     debugPrint("Verbruik naar RAM (charge in mAh): ");
     debugPrintln(charge);

  }  else {        // I2C error
     if (chargeState == true) {  // Accu is los(1e keer gedetecteerd)
        debugPrintln("Case 3");

        params.setChargeOffset(charge); // charge -> flash (eenmalig)
        debugPrint("Verbruik van RAM naar Flash (chargeOffset in mAh): ");
        debugPrintln(charge);

        chargeState = false; 
     } else {
        debugPrintln("Case 4");
     }
  }

  current = LTC2943_code_to_current(current_code, resistor);                //! Convert current code to Amperes
  voltage = LTC2943_code_to_voltage(voltage_code);                          //! Convert voltage code to Volts
  temperature = LTC2943_code_to_celcius_temperature(temperature_code);      //! Convert temperature code to Celcius

  if (ack > 0) 
  {
    valid = false;
  }
  return (ack);
}

// Write an 8-bit code to the LTC2943.
int8_t LTC::LTC2943_write(uint8_t i2c_address, uint8_t adc_command, uint8_t code)
// The function returns the state of the acknowledge bit after the I2C address write. 0=acknowledge, 1=no acknowledge.
{
  int32_t ack;

  ack = i2c_write_byte_data(i2c_address, adc_command, code);
  return(ack);
}

// Reads an 8-bit adc_code from LTC2943
int8_t LTC::LTC2943_read(uint8_t i2c_address, uint8_t adc_command, uint8_t *adc_code)
// The function returns the state of the acknowledge bit after the I2C address write. 0=acknowledge, 1=no acknowledge.
{
  int32_t ack;

  ack = i2c_read_byte_data(i2c_address, adc_command, adc_code);

  return(ack);
}

// Reads a 16-bit adc_code from LTC2943
int8_t LTC::LTC2943_read_16_bits(uint8_t i2c_address, uint8_t adc_command, uint16_t *adc_code)
// The function returns the state of the acknowledge bit after the I2C address write. 0=acknowledge, 1=no acknowledge.
{
  int32_t ack;

  ack = i2c_read_word_data(i2c_address, adc_command, adc_code);

  return(ack);
}

float LTC::LTC2943_code_to_mAh(uint16_t adc_code, float resistor, uint16_t prescalar )
// The function converts the 16-bit RAW adc_code to mAh
{
  float mAh_charge;
  mAh_charge = 1000*(float)((adc_code-32767)*LTC2943_CHARGE_lsb*prescalar*50E-3)/(resistor*4096);
  return(mAh_charge);
}

float LTC::LTC2943_code_to_voltage(uint16_t adc_code)
// The function converts the 16-bit RAW adc_code to Volts
{
  float voltage;
  voltage = ((float)adc_code/(65535))*LTC2943_FULLSCALE_VOLTAGE;
  return(voltage);
}

float LTC::LTC2943_code_to_current(uint16_t adc_code, float resistor)
// The function converts the 16-bit RAW adc_code to Amperes
{
  float current;
  current = (((float)adc_code-32767)/(32767))*((float)(LTC2943_FULLSCALE_CURRENT)/resistor);
  return(current);
}

float LTC::LTC2943_code_to_celcius_temperature(uint16_t adc_code)
// The function converts the 16-bit RAW adc_code to Celcius
{
  float temperature;
  temperature = adc_code*((float)(LTC2943_FULLSCALE_TEMPERATURE)/65535) - 273.15;
  return(temperature);
}

// Write a byte of data to register specified by "command"
int8_t LTC::i2c_write_byte_data(uint8_t address, uint8_t command, uint8_t value)
{
  int8_t ret = 0;

  Wire.beginTransmission(address);
  ret += Wire.write(command);                 // Set register to be read to command
  ret += Wire.write(value);
  Wire.endTransmission();                        // I2C STOP

  if (ret!=2)                         //If there was a NAK return 1
    return(1);
  return(0);                     // Return success
}

// Read a byte of data at register specified by "command", store in "value"
int8_t LTC::i2c_read_byte_data(uint8_t address, uint8_t command, uint8_t *value)
{
  int8_t ret = 0;
  union
  {
    uint8_t b[1];
    uint8_t w;
  } data;

  Wire.beginTransmission(address);
  ret += Wire.write(command);
  Wire.endTransmission(false);
  
  Wire.requestFrom(address,1);
  if (1 <= Wire.available()) {
    data.b[0] = Wire.read(); 
    ret++;                                      // Return success
  }
  *value = data.w;

  if (ret!=2)                         //If NAK
    return (1);                     //return 1
  return(0);                                      // Return success
}

// Read a 16-bit word of data from register specified by "command"
int8_t LTC::i2c_read_word_data(uint8_t address, uint8_t command, uint16_t *value)
{
  int8_t ret = 0;

  union
  {
    uint8_t b[2];
    uint16_t w;
  } data;

  Wire.beginTransmission(address);
  ret += Wire.write(command);                 // Set register to be read to command
  Wire.endTransmission(false);

  Wire.requestFrom(address,2);
  if (2 <= Wire.available()) { // if two bytes were received
    data.b[1] = Wire.read();  // receive high byte (overwrites previous reading)
    data.b[0] = Wire.read(); // receive low byte as lower 8 bits
    ret++;
  }

  *value = data.w;

  if (ret!=2)                         //If NAK
    return (1);                     //return 1
  return(0);                                      // Return success
}


