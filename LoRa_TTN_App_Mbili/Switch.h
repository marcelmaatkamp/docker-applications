#ifndef Switch_H
#define Switch_H

#include <Arduino.h>

class Switch {
	public:
	  Switch(int pin);
	  Switch();
	  ~Switch();
	  int  ReadState();
	  void Update();
  
    private:
	  // Class Member Variables
	  // These are initialized at startup
	  int counter = 0;       // how many times we have seen new value
	  int reading;           // the current value read from the input pin
	  int current_state = LOW;    // the debounced input value
	  int inputPin;    // the number of the pin the Switch is connected to

	  int debounce_count = 3; // number of millis/samples to consider before declaring a debounced input
};

#endif

