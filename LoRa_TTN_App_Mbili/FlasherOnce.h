#ifndef FlasherOnce_H
#define FlasherOnce_H

#include <Arduino.h>

class FlasherOnce {
  public:
    FlasherOnce(int pin, long onTimeValue);
    FlasherOnce();
    ~FlasherOnce();
    void Update();
    void Flash();
  
  private: 
   // Class Member Variables
    // These are initialized at startup
    int ledPin;      // the number of the LED pin
    long OnTime;     // milliseconds of on-time
  
    // These maintain the current state
    int ledState;                 // To hold the state of the LED
    unsigned long previousMillis; // To store the time the LED was turned on

  
};

#endif

