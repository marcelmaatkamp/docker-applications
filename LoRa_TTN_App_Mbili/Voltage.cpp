#include "Voltage.h"

/**************************************************************************************************
   Voltage class - Used to read the current Voltage level of a connected Baattery
                   As result the average is retruned of 10 readings
***************************************************************************************************/

//<<constructor>>
Voltage::Voltage(int pin)
{
    inputPin = pin;
}
 
//<<destructor>>
Voltage::~Voltage(){/*nothing to destruct*/}
 
float Voltage::Read()
{
   float result;
   long sensorValue=analogRead(inputPin);
   long sum=0;
   for(int i=0;i<10;i++)  // Read 10 times, and take average
   {  
      sum=sensorValue+sum;
      sensorValue=analogRead(inputPin);
      delay(2);
   }   
   sum=sum/10;
   
   // Input divided by 4 by resitors
   // Input is handled by 10 bit (max. decimal value = 1023) ADC in Mbili,
   // so 1023 is equivalent to 3.3V
   result = 4*sum*3.3/1023.00; //Mbili = 3.3V dus 3300mV komt overeen met 1023

//   Serial.print("Input voltage = ");
//   Serial.print(4*sum*3.3/1023.00); //Mbili = 3.3V dus 3300mV komt overeen met 1023
//   Serial.println(" Volt");

   return result;
}

char * Voltage::Read2()
{
   static char resultBuffer[6];  // to hold result of measurement
   float  result;
   long   sensorValue=analogRead(inputPin);
   long   sum=0;
   for(int i=0;i<10;i++)  // Read 10 times, and take average
   {  
      sum=sensorValue+sum;
      sensorValue=analogRead(inputPin);
      delay(2);
   }   
   sum=sum/10;
   
   // Input divided by 4 by resitors
   // Input is handled by 10 bit (max. decimal value = 1023) ADC in Mbili,
   // so 1023 is equivalent to 3.3V
   result = 4*sum*3.3/1023.00; //Mbili = 3.3V dus 3300mV komt overeen met 1023

//   Serial.print("Input voltage = ");
//   Serial.print(4*sum*3.3/1023.00); //Mbili = 3.3V dus 3300mV komt overeen met 1023
//   Serial.println(" Volt");

 
//    static char dtostrfbuffer[6];
    dtostrf(result,5, 2, resultBuffer); // 5 = wide, 2 = precision

   return resultBuffer;
}






