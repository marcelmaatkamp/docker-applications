#include "FlasherOnce.h"

/*******************************************************************************
   FlasherOnce class - Used to Flash a Led Once. The ON-time can be configured.
*******************************************************************************/

//<<constructor>>
FlasherOnce::FlasherOnce(int pin, long onTimeValue)
{
    ledPin = pin;
    pinMode(ledPin, OUTPUT);
    digitalWrite(ledPin, LOW); 
    OnTime = onTimeValue;
    ledState = LOW; 
    previousMillis = 0;
	return;
}
 
//<<destructor>>
FlasherOnce::~FlasherOnce(){/*nothing to destruct*/}
 
void FlasherOnce::Update()
{
    // check to see if it's time to change the state of the LED
    unsigned long currentMillis = millis();
     
    if((ledState == HIGH) && (currentMillis - previousMillis >= OnTime))
    {
      ledState = LOW;  // Turn it off
      digitalWrite(ledPin, ledState);  // Update the actual LED
    }
}

void FlasherOnce::Flash()
{
      previousMillis = millis();
      ledState = HIGH;  // Turn it on
      digitalWrite(ledPin, ledState);  // Update the actual LED
}






