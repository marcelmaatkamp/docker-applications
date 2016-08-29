#ifndef Voltage_H
#define Voltage_H

#include <Arduino.h>
#include <Stream.h>
#include "Devices.h"

#define ADC_AREF 3.3f
#define BATVOLT_R1 6.0f
#define BATVOLT_R2 2.0f

#ifdef DEBUG
#define debugPrintLn(...) { if (this->diagStream) this->diagStream->println(__VA_ARGS__); }
#define debugPrint(...) { if (this->diagStream) this->diagStream->print(__VA_ARGS__); }
#warning "Debug mode is ON"
#else
#define debugPrintLn(...)
#define debugPrint(...)
#endif

class Voltage {
  public:
    Voltage(int pin);
    ~Voltage();
    void Update();
    String getData();
    void setPin(int pin);
    // Sets the optional "Diagnostics and Debug" stream.
    void setDiag(Stream& stream) { diagStream = &stream; };
  
  private:
    // Class Member Variables
    // These are initialized at startup
    int inputPin = -1;    // the number of the pin the Temp sensor is connected to
    String voltage;
//    long value;

    // The (optional) stream to show debug information.
    Stream* diagStream;
};

#endif

