//#include <Sodaq_RN2483.h>
#include "Flasher.h"


/**************************************************************************************************
   Flasher class - Used to Flash a Led On and Off. The ON-time as well as the OFF-timne can be set.
***************************************************************************************************/


//<<constructor>>
Flasher::Flasher(int pin, long onTimeValue, long offTimeValue)
{
    ledPin = pin;
    pinMode(ledPin, OUTPUT);
    digitalWrite(ledPin, LOW); 
      
    OnTime = onTimeValue;
    OffTime = offTimeValue;
    
    ledState = LOW; 
    previousMillis = 0;
    return;
}
 
//<<destructor>>
Flasher::~Flasher(){/*nothing to destruct*/}
 
void Flasher::Update()
{
    // check to see if it's time to change the state of the LED
    unsigned long currentMillis = millis();
     
    if((ledState == HIGH) && (currentMillis - previousMillis >= OnTime))
    {
      ledState = LOW;  // Turn it off
      previousMillis = currentMillis;  // Remember the time
      digitalWrite(ledPin, ledState);  // Update the actual LED
    }
    else if ((ledState == LOW) && (currentMillis - previousMillis >= OffTime))
    {
      ledState = HIGH;  // turn it on
      previousMillis = currentMillis;   // Remember the time
      digitalWrite(ledPin, ledState);   // Update the actual LED
    }
}
