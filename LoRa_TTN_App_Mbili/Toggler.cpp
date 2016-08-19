#include "Toggler.h"

/**************************************************************************************************
   Toggler class - Used to Toggle a Led
***************************************************************************************************/

//<<constructor>>
Toggler::Toggler(int pin)
{
    ledPin = pin;
    pinMode(ledPin, OUTPUT);
    digitalWrite(ledPin, LOW); 
    ledState = LOW; 
}
 
//<<destructor>>
Toggler::~Toggler(){/*nothing to destruct*/}
 
void Toggler::Toggle()
{
    if(ledState == HIGH) {
      ledState = LOW;  // Turn it off
      digitalWrite(ledPin, ledState);
    } else {
      ledState = HIGH;  // turn it on
      digitalWrite(ledPin, ledState);
    }
}







