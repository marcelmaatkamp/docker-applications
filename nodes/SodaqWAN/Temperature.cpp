#include "Temperature.h"

/*******************************************************************************
   Temperature class  - Can be used to read the state of a Temperature (sensor). 
                   A software debounce construction is implemented as follows:
                   At each transition from LOW to HIGH or from HIGH to LOW 
                   the input signal is debounced by sampling across
                   multiple reads over several invocations of the update() method.
                   The input is not considered HIGH or LOW until the input signal 
                   has been sampled for at least "debounce_count" (10)
                   milliseconds in the new state.

*******************************************************************************/
DHT dht(DHTPIN, DHTTYPE);

//<<constructor>>
Temperature::Temperature(int pin)
{
  setPin(pin);
}
 
//<<destructor>>
Temperature::~Temperature(){/*nothing to destruct*/}
 
String Temperature::readTemp()
{
	return tempValue;
}

String Temperature::getData()
{
  return data;
}

void Temperature::Update()
{
  // Reading temperature or humidity takes about 250 milliseconds!
  // Sensor readings may also be up to A0 seconds 'old' (its a very slow sensor)
  temp = dht.readTemperature();
  hum = dht.readHumidity();
  if (isnan(temp) || isnan(hum)) {
    tempValue = "NAN";
    data = "NAN";
  } else {
    tempValue = String(temp);
    data = String(temp) + ";" + String(hum);
  } 
}

void Temperature::setPin(int pin)
{
  if (pin > -1) 
  {
    inputPin = pin;
    pinMode(inputPin, INPUT);
  }
}

