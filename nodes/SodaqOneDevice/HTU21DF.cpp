/*************************************************** 
  This is a library for the HTU21DF Humidity & Temp Sensor

  Designed specifically to work with the HTU21DF sensor from Adafruit
  ----> https://www.adafruit.com/products/1899

  These displays use I2C to communicate, 2 pins are required to  
  interface
  Adafruit invests time and resources providing this open source code, 
  please support Adafruit and open-source hardware by purchasing 
  products from Adafruit!

  Written by Limor Fried/Ladyada for Adafruit Industries.  
  BSD license, all text above must be included in any redistribution
 ****************************************************/

#include "HTU21DF.h"
#if defined(__AVR__)
#include <util/delay.h>
#endif

HTU21DF::HTU21DF() {
}

boolean HTU21DF::begin(void) {
  Wire.begin();
  
  reset();

  Wire.beginTransmission(HTU21DF_I2CADDR);
  Wire.write(HTU21DF_READREG);
  Wire.endTransmission();
  Wire.requestFrom(HTU21DF_I2CADDR, 1);
  return (Wire.read() == 0x2); // after reset should be 0x2
}

void HTU21DF::reset(void) {
  Wire.beginTransmission(HTU21DF_I2CADDR);
  Wire.write(HTU21DF_RESET);
  Wire.endTransmission();
  delay(15);
}

// Sets the optional "Diagnostics and Debug" stream.
void HTU21DF::setDiag(Stream& stream, bool debug) 
{ 
  diagStream = &stream; 
  debug = debug;
}

void HTU21DF::Update()
{
  this->readTemperature();
  this->readHumidity();
  if (isnan(this->temp) || isnan(this->humidity)) {
    valid = false;
  } else {
    valid = true;
  } 
  return;
}

void HTU21DF::readTemperature(void) {
  temp=-1;
  uint8_t crc;
  uint16_t t;
  
  // OK lets ready!
  Wire.beginTransmission(HTU21DF_I2CADDR);
  Wire.write(HTU21DF_READTEMP);
  Wire.endTransmission();
  
  delay(50); // add delay between request and actual read!
  
  Wire.requestFrom(HTU21DF_I2CADDR, 3);
  if (Wire.available()) {
    t = Wire.read();
    t <<= 8;
    t |= Wire.read();
  
    crc = Wire.read();
  
    temp = t;
    temp *= 175.72;
    temp /= 65536;
    temp -= 46.85;
  }
}
  
void HTU21DF::readHumidity(void) 
{
  uint16_t h;
  uint8_t crc;
  humidity=-1;
  
  // OK lets ready!
  Wire.beginTransmission(HTU21DF_I2CADDR);
  Wire.write(HTU21DF_READHUM);
  Wire.endTransmission();
  
  delay(50); // add delay between request and actual read!
  
  Wire.requestFrom(HTU21DF_I2CADDR, 3);
  if (Wire.available())
  {
    h = Wire.read();
    h <<= 8;
    h |= Wire.read();
  
    crc = Wire.read();
  
    humidity = h;
    humidity *= 125;
    humidity /= 65536;
    humidity -= 6;
  }
}



/*********************************************************************/
