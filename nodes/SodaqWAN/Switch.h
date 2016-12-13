#ifndef Switch_H
#define Switch_H

#include <Arduino.h>
#include <Stream.h>

#ifdef DEBUG
#define debugPrintLn(...) { if (this->diagStream) this->diagStream->println(__VA_ARGS__); }
#define debugPrint(...) { if (this->diagStream) this->diagStream->print(__VA_ARGS__); }
#warning "Debug mode is ON"
#else
#define debugPrintLn(...)
#define debugPrint(...)
#endif

class Switch {
  public:
    Switch(int pin);
    ~Switch();
    int  ReadState();
    void Update();
    bool isChanged();
    void setPin(int pin);
    // Sets the optional "Diagnostics and Debug" stream.
    void setDiag(Stream& stream) {
      diagStream = &stream;
    };

  private:
    // Class Member Variables
    // These are initialized at startup
    int counter = 0;       // how many times we have seen new value
    int reading;           // the current value read from the input pin
    int current_state = LOW;    // the debounced input value
    int inputPin = -1;    // the number of the pin the Switch is connected to
    int switchState = 0;
    int switchOldState = 0;

    int debounce_count = 3; // number of millis/samples to consider before declaring a debounced input
    // The (optional) stream to show debug information.
    Stream* diagStream;
};

#endif

