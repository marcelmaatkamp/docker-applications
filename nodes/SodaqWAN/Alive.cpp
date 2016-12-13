#include "Alive.h"

/*******************************************************************************
   Alive class  - Can be used to read the state of a Alive (sensor). 
                   A software debounce construction is implemented as follows:
                   At each transition from LOW to HIGH or from HIGH to LOW 
                   the input signal is debounced by sampling across
                   multiple reads over several invocations of the update() method.
                   The input is not considered HIGH or LOW until the input signal 
                   has been sampled for at least "debounce_count" (10)
                   milliseconds in the new state.

*******************************************************************************/

//<<constructor>>
Alive::Alive(long AliveFrequencyMillisValue)
{
  AliveFrequencyMillis = AliveFrequencyMillisValue;
  setCurrentTime();
  timePassed = false;
}
 
//<<destructor>>
Alive::~Alive(){/*nothing to destruct*/}
 
void Alive::Update()
{
  // check to see if it's time to change the state of the LED
  unsigned long currentMillis = millis();

  if(currentMillis - previousMillis >= AliveFrequencyMillis)
  {
    setCurrentTime();
    timePassed = true;
  }
}

void Alive::setCurrentTime()
{
  previousMillis = millis();
}

