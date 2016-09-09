#include "Voltage.h"

/*******************************************************************************
   Voltage class  - Can be used to read the state of a Voltage (sensor). 
                   A software debounce construction is implemented as follows:
                   At each transition from LOW to HIGH or from HIGH to LOW 
                   the input signal is debounced by sampling across
                   multiple reads over several invocations of the update() method.
                   The input is not considered HIGH or LOW until the input signal 
                   has been sampled for at least "debounce_count" (10)
                   milliseconds in the new state.

*******************************************************************************/

//<<constructor>>
Voltage::Voltage(int pin)
{
  setPin(pin);
}
 
//<<destructor>>
Voltage::~Voltage(){/*nothing to destruct*/}
 
String Voltage::getData()
{
  return voltage;
}

int Voltage::getValue()
{
  return (int)(value*100);
}

void Voltage::Update()
{
  long   sensorValue=analogRead(inputPin);
  long   sum=0;
  for(int i=0;i<10;i++)  // Read 10 times, and take average
  {  
    sum=sensorValue+sum;
    sensorValue=analogRead(inputPin);
    delay(2);
  }   
  sum=sum/10;
  value = 4*sum*3.3/1023.00; //Mbili = 3.3V dus 3300mV komt overeen met 1023

  voltage = String(value);
}

void Voltage::setPin(int pin)
{
  if (pin > -1) 
  {
    inputPin = pin;
    pinMode(inputPin, INPUT);
  }
}

