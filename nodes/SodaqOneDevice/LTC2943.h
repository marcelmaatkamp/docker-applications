/*!
LTC2943: Multicell Battery Gas Gauge with Temperature, Voltage and Current Measurement.
LTC2943-1: Multicell Battery Gas Gauge with Temperature, Voltage and Current Measurement.

I2C DATA FORMAT (MSB FIRST):

Data Out:
Byte #1                                    Byte #2                     Byte #3

START  SA6 SA5 SA4 SA3 SA2 SA1 SA0 W SACK  C7  C6 C5 C4 C3 C2 C1 C0 SACK D7 D6 D5 D4 D3 D2 D1 D0 SACK  STOP

Data In:
Byte #1                                    Byte #2                                    Byte #3

START  SA6 SA5 SA4 SA3 SA2 SA1 SA0 W SACK  C7  C6  C5 C4 C3 C2 C1 C0 SACK  Repeat Start SA6 SA5 SA4 SA3 SA2 SA1 SA0 R SACK

Byte #4                                   Byte #5
MSB                                       LSB
D15 D14  D13  D12  D11  D10  D9 D8 MACK   D7 D6 D5 D4 D3  D2  D1  D0  MNACK  STOP

START       : I2C Start
Repeat Start: I2c Repeat Start
STOP        : I2C Stop
SAx         : I2C Address
SACK        : I2C Slave Generated Acknowledge (Active Low)
MACK        : I2C Master Generated Acknowledge (Active Low)
MNACK       : I2c Master Generated Not Acknowledge
W           : I2C Write (0)
R           : I2C Read  (1)
Cx          : Command Code
Dx          : Data Bits
X           : Don't care


*/


#ifndef LTC2943_H
#define LTC2943_H

#include <Arduino.h>
#include <Wire.h>

/*! @name LTC2943 I2C Address
@{ */

#define LTC2943_I2C_ADDRESS 0x64
#define LTC2943_I2C_ALERT_RESPONSE  0x0C
//! @}


// Registers
#define LTC2943_STATUS_REG                          0x00
#define LTC2943_CONTROL_REG                         0x01
#define LTC2943_ACCUM_CHARGE_MSB_REG                0x02
#define LTC2943_ACCUM_CHARGE_LSB_REG                0x03
#define LTC2943_CHARGE_THRESH_HIGH_MSB_REG          0x04
#define LTC2943_CHARGE_THRESH_HIGH_LSB_REG          0x05
#define LTC2943_CHARGE_THRESH_LOW_MSB_REG           0x06
#define LTC2943_CHARGE_THRESH_LOW_LSB_REG           0x07
#define LTC2943_VOLTAGE_MSB_REG                     0x08
#define LTC2943_VOLTAGE_LSB_REG                     0x09
#define LTC2943_VOLTAGE_THRESH_HIGH_MSB_REG         0x0A
#define LTC2943_VOLTAGE_THRESH_HIGH_LSB_REG         0x0B
#define LTC2943_VOLTAGE_THRESH_LOW_MSB_REG          0x0C
#define LTC2943_VOLTAGE_THRESH_LOW_LSB_REG          0x0D
#define LTC2943_CURRENT_MSB_REG                     0x0E
#define LTC2943_CURRENT_LSB_REG                     0x0F
#define LTC2943_CURRENT_THRESH_HIGH_MSB_REG         0x10
#define LTC2943_CURRENT_THRESH_HIGH_LSB_REG         0x11
#define LTC2943_CURRENT_THRESH_LOW_MSB_REG          0x12
#define LTC2943_CURRENT_THRESH_LOW_LSB_REG          0x13
#define LTC2943_TEMPERATURE_MSB_REG                 0x14
#define LTC2943_TEMPERATURE_LSB_REG                 0x15
#define LTC2943_TEMPERATURE_THRESH_HIGH_REG         0x16
#define LTC2943_TEMPERATURE_THRESH_LOW_REG          0x17

// Command Codes
#define LTC2943_AUTOMATIC_MODE                  0xC0
#define LTC2943_SCAN_MODE                       0x80
#define LTC2943_MANUAL_MODE                     0x40
#define LTC2943_SLEEP_MODE                      0x00

#define LTC2943_PRESCALAR_M_1                   0x00
#define LTC2943_PRESCALAR_M_4                   0x08
#define LTC2943_PRESCALAR_M_16                  0x10
#define LTC2943_PRESCALAR_M_64                  0x18
#define LTC2943_PRESCALAR_M_256                 0x20
#define LTC2943_PRESCALAR_M_1024                0x28
#define LTC2943_PRESCALAR_M_4096                0x30
#define LTC2943_PRESCALAR_M_4096_2              0x31

#define LTC2943_ALERT_MODE                      0x04
#define LTC2943_CHARGE_COMPLETE_MODE            0x02

#define LTC2943_DISABLE_ALCC_PIN                0x00
#define LTC2943_SHUTDOWN_MODE                   0x01

const float LTC2943_CHARGE_lsb = 0.34E-3;
const float LTC2943_VOLTAGE_lsb = 1.44E-3;
const float LTC2943_CURRENT_lsb = 29.3E-6;
const float LTC2943_TEMPERATURE_lsb = 0.25;
const float LTC2943_FULLSCALE_VOLTAGE = 23.6;
const float LTC2943_FULLSCALE_CURRENT = 60E-3;
const float LTC2943_FULLSCALE_TEMPERATURE = 510;

// Change resistor value to 50 mOhm (0.05) for DC1812AC (LTC2943-1)
const float resistor = .05;                               //!< resistor value on demo board

#ifdef DEBUG
#define debugPrintLn(...) { if (this->diagStream) this->diagStream->println(__VA_ARGS__); }
#define debugPrint(...) { if (this->diagStream) this->diagStream->print(__VA_ARGS__); }
#warning "Debug mode is ON"
#else
#define debugPrintLn(...)
#define debugPrint(...)
#endif

class LTC {
public:
  LTC(int pin);
  ~LTC();
      
  int8_t Update();
  
  float getCharge() {return charge;};
  float getCurrent() {return current;};
  float getVoltage() {return voltage;};
  float getTemp() {return temperature;};

  bool isValid() {return valid;};
  
  // Sets the optional "Diagnostics and Debug" stream.
  void setDiag(Stream& stream);
  void setPin(int pin);
  int8_t ProcessInterrupt();

private:
  // The (optional) stream to show debug information.
  Stream* diagStream;
  float charge, current, voltage, temperature;
  int inputPin = -1;    // the number of the pin the Switch is connected to
  bool valid=false;
  int IRSctr;  // To see the number of interrupts occurred (for debugging purposes)
    
  //! Write an 8-bit code to the LTC2943.
  //! @return The function returns the state of the acknowledge bit after the I2C address write. 0=acknowledge, 1=no acknowledge.
  int8_t LTC2943_write(uint8_t i2c_address, //!< Register address for the LTC2943
                       uint8_t adc_command, //!< The "command byte" for the LTC2943
                       uint8_t code         //!< Value that will be written to the register.
                      );
  
  //! Reads an 8-bit adc_code from LTC2943
  //! @return The function returns the state of the acknowledge bit after the I2C address write. 0=acknowledge, 1=no acknowledge.
  int8_t LTC2943_read(uint8_t i2c_address, //!< Register address for the LTC2943
                      uint8_t adc_command, //!< The "command byte" for the LTC2943
                      uint8_t *adc_code    //!< Value that will be read from the register.
                     );
  
  //! Reads a 16-bit adc_code from LTC2943
  //! @return The function returns the state of the acknowledge bit after the I2C address write. 0=acknowledge, 1=no acknowledge.
  int8_t LTC2943_read_16_bits(uint8_t i2c_address, //!< Register address for the LTC2943
                              uint8_t adc_command, //!< The "command byte" for the LTC2943
                              uint16_t *adc_code   //!< Value that will be read from the register.
                             );

  //! Reads a 8-bit adc_code from LTC2943 with the Special address 0C to follow the Alert Response Protocol
  //! @return The function returns the state of the acknowledge bit after the I2C address write. 0=acknowledge, 1=no acknowledge.
  int8_t LTC2943_arp(uint8_t i2c_address, //!< i2c address
                     uint8_t *adc_code    //!< Value that will be read.
                    );

  
  //! Calculate the LTC2943 charge in mAh
  //! @return Returns the Coulombs of charge in the ACR register.
  float LTC2943_code_to_mAh(uint16_t adc_code,            //!< The RAW ADC value
                            float resistor,       //!< The sense resistor value
                            uint16_t prescalar    //!< The prescalar value
                           );
  
  //! Calculate the LTC2943 SENSE+ voltage
  //! @return Returns the SENSE+ Voltage in Volts
  float LTC2943_code_to_voltage(uint16_t adc_code              //!< The RAW ADC value
                               );
  
  //! Calculate the LTC2943 current with a sense resistor
  //! @return Returns the current through the sense resistor
  float LTC2943_code_to_current(uint16_t adc_code,                //!< The RAW ADC value
                                float resistor                   //!< The sense resistor value
                               );
  
  //! Calculate the LTC2943 temperature
  //! @return Returns the temperature in Celcius
  float LTC2943_code_to_celcius_temperature(uint16_t adc_code          //!< The RAW ADC value
                                           );
  //! Write a byte of data to register specified by "command"
  //! @return 0 on success, 1 on failure
  int8_t i2c_write_byte_data(uint8_t address,    //!< 7-bit I2C address
                             uint8_t command,  //!< Command byte
                             uint8_t value     //!< Byte to be written
                            );
  
  //! Read a byte of data at register specified by "command", store in "value"
  //! @return 0 on success, 1 on failure
  int8_t i2c_read_byte_data(uint8_t address,     //!< 7-bit I2C address
                            uint8_t command,   //!< Command byte
                            uint8_t *value     //!< Byte to be read
                           );
  
  //! Read a 16-bit word of data from register specified by "command"
  //! @return 0 on success, 1 on failure
  int8_t i2c_read_word_data(uint8_t address,     //!< 7-bit I2C address
                            uint8_t command,   //!< Command byte
                            uint16_t *value    //!< Word to be read
                           );

  //! Read a byte of data from the Special address 0C to follow the Alert Response Protocol
  //! @return 0 on success, 1 on failure
  int8_t i2c_arp(uint8_t address, //!< 7-bit I2C address
                 uint8_t *value   //!< Byte to be read
                );

};

#endif  // LTC2943_H
