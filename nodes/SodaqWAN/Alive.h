#ifndef Alive_H
#define Alive_H

#include <Stream.h>
#include "Devices.h"

#ifdef DEBUG
#define debugPrintLn(...) { if (this->diagStream) this->diagStream->println(__VA_ARGS__); }
#define debugPrint(...) { if (this->diagStream) this->diagStream->print(__VA_ARGS__); }
#warning "Debug mode is ON"
#else
#define debugPrintLn(...)
#define debugPrint(...)
#endif

class Alive {
  public:
    Alive(long AliveFrequencyMillisValue);
    ~Alive();
    void Update();
    void setCurrentTime();
    // Sets the optional "Diagnostics and Debug" stream.
    inline void setDiag(Stream& stream) {diagStream = &stream;};
    inline bool isTimePassed() {return timePassed;};
    inline void resetTimePassed() {timePassed = false;};
  
  private:
    // Class Member Variables
    // These are initialized at startup
    unsigned long AliveFrequencyMillis;     // period of time (msecs) between transmission of 2 alive messages
  
    // These maintain the current state
    unsigned long previousMillis;   // will store last time an Alive message was send
    bool timePassed;
    
    // The (optional) stream to show debug information.
    Stream* diagStream;
};

#endif

