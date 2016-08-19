#ifndef Toucher_H
#define Toucher_H

#include <Arduino.h>

class Toucher {
	public:
	  Toucher(int pin, long sampleTimeValue);
	  Toucher();
	  ~Toucher();
	  void Reset();
	  int  ReadButtonState();
	  void Update();
  
    private:
	  // Class Member Variables
	  // These are initialized at startup
	  int buttonPin;    // the number of the pin the Touchsensor is connected to
	  long sampleTime;  // milliseconds between 2 readings
	  //long OffTime;     // milliseconds of off-time

	  // These maintain the current state
	  int buttonState;
	  unsigned long previousMillis;   // will store last time Touch sensor was read
};

#endif

