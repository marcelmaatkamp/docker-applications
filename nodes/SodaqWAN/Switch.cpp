#include "Switch.h"

/*******************************************************************************
   Switch class  - Can be used to read the state of a Switch (sensor).
                   A software debounce construction is implemented as follows:
                   At each transition from LOW to HIGH or from HIGH to LOW
                   the input signal is debounced by sampling across
                   multiple reads over several invocations of the update() method.
                   The input is not considered HIGH or LOW until the input signal
                   has been sampled for at least "debounce_count" (10)
                   milliseconds in the new state.

*******************************************************************************/

//<<constructor>>
Switch::Switch(int pin)
{
  setPin(pin);
}

//<<destructor>>
Switch::~Switch() {
  /*nothing to destruct*/
}

int Switch::ReadState()
{
  return current_state;
}

void Switch::Update()
{
  if (inputPin > -1)
  {
    reading = digitalRead(inputPin);
    if (reading == current_state && counter > 0)
    {
      counter--;
    }
    if (reading != current_state)
    {
      counter++;
    }
    if (counter != 0) {
      debugPrint("Counter = ");
      debugPrintLn(counter);
    }

    // If the Input has shown the same value for long enough let's switch it
    if (counter >= debounce_count)
    {
      counter = 0;
      current_state = reading;
      debugPrint("State of ");
      debugPrint(inputPin);
      debugPrintLn(" = stable");
    }
  }
}

bool Switch::isChanged()
{
  //Read the state of the microSwitch
  switchState = ReadState();
  if (switchState != switchOldState) {
    switchOldState = switchState;
    return true;
  }
  return false;
}

void Switch::setPin(int pin)
{
  if (pin > -1)
  {
    inputPin = pin;
    pinMode(inputPin, INPUT);
  }
}

