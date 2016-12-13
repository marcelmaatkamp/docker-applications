#ifndef Toggler_H
#define Toggler_H

#include <Arduino.h>

class Toggler {
	public:
	  Toggler(int pin);
	  Toggler();
	  ~Toggler();
	  void Toggle();
  
    private:
      // Class Member Variables
      // These are initialized at startup
      int ledPin;      // the number of the LED pin
      int ledState;                 // ledState used to set the LED
};

#endif

