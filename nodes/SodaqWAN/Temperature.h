#ifndef Temperature_H
#define Temperature_H

#include <Arduino.h>
#include <Stream.h>
#include <DHT.h>
#include "Devices.h"

#ifdef DEBUG
#define debugPrintLn(...) { if (this->diagStream) this->diagStream->println(__VA_ARGS__); }
#define debugPrint(...) { if (this->diagStream) this->diagStream->print(__VA_ARGS__); }
#warning "Debug mode is ON"
#else
#define debugPrintLn(...)
#define debugPrint(...)
#endif

class Temperature {
	public:
    Temperature(int pin);
	  ~Temperature();
	  void Update();
    String readTemp();
    int getTemp();
    int getHumidity();
    inline bool isValid() { return validData; };
    String getData();
    void setPin(int pin);
    // Sets the optional "Diagnostics and Debug" stream.
    void setDiag(Stream& stream) { diagStream = &stream; };
  
  private:
	  // Class Member Variables
	  // These are initialized at startup
	  int inputPin = -1;    // the number of the pin the Temp sensor is connected to
    float temp;
    float hum;
    String tempValue;
    String data;
    bool validData=true;

    // The (optional) stream to show debug information.
    Stream* diagStream;
};

#endif

