#ifndef Flasher_H
#define Flasher_H

#include <Arduino.h>

class Flasher {
  public:
    Flasher(int pin, long onTimeValue, long offTimeValue);
    Flasher();
    ~Flasher();
    void Update();
  
  private:
    int  ledPin;      // the number of the LED pin
    long OnTime;     // milliseconds of on-time
    long OffTime;    // milliseconds of off-time
  
    // These maintain the current state
    int ledState;                 // ledState used to set the LED
    unsigned long previousMillis;   // will store last time LED was updated

};

#endif

