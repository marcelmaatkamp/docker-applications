#ifndef Voltage_H
#define Voltage_H

#include <Arduino.h>

class Voltage {
	public:
	  Voltage(int pin);
	  Voltage();
	  ~Voltage();
    float Read();
    char* Read2();
  
    private:
      // Class Member Variables
      // These are initialized at startup
      int  inputPin;         // the number of the LED pin
      //char resultBuffer[6];  // to hold result of measurement
};

#endif

