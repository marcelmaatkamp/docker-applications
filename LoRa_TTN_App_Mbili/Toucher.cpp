#include "Toucher.h"

/*******************************************************************************
   Toucher class - Can be used to read a Touch button (sensor) state. 
                   The buttonState is set to HIGH on a rising edge of the sensor signal.
                   The buttonState can be read by the user of this class by calling the ReturnButtonState() method).
                   The buttonState remains HIGH until the Reset() method of this class is called.
*******************************************************************************/

//<<constructor>>
Toucher::Toucher(int pin, long sampleTimeValue)
{
    buttonPin = pin;
    pinMode(buttonPin, INPUT);
    sampleTime = sampleTimeValue;
    buttonState = HIGH; 
    previousMillis = 0;
}
 
//<<destructor>>
Toucher::~Toucher(){/*nothing to destruct*/}
 
void Toucher::Reset()
{
    buttonState = HIGH;
}

int Toucher::ReadButtonState()
  {
    return buttonState;
}

void Toucher::Update()
{
    // TODO: handle overflow
    // check to see if it's time to read the status of the Touch sensor and
    // update the buttonState accordingly
    unsigned long currentMillis = millis();

    if ((buttonState == HIGH) && (currentMillis - previousMillis >= sampleTime)) {
       buttonState = digitalRead(buttonPin);
       if (buttonState == LOW) {
       //   debugSerial.println("Touched the sensor!");
       }
    }
}
