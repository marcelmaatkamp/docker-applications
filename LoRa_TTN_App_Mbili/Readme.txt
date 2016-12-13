File:       Readme.txt
Author:     Theo
Content:    Description of the Sodaq MBili program.
Date:       10 aug 2016

This document described the C++ software (Arduino IDE sketch) that can be loaded on an Sodaq Mbili
deveopment board to transfer the board (with RN2483 LoRaBee) into a sensor platform with LoRa communication.
The Sodaq MBili is a development platform that has Grove connectors on it that can be used to connect
all kind of sensor modules or other external devices to the MCU.
A serial USB interface is avaliable to program the MCU. This interface can also be used to monitor
the execution of the program. Logging and Debugging Statements in the program are send to this interface
by the means of debugSerial.print() and debugSerial.println() statements.

The (Main) sketch is:           LoRa_TTN_App_Mbili.ino
Helper classes are put in:      *.cpp and corresponding *.h files
The Sodaq Mbili libraries:      Sodaq_RN2483.h      

Main sketch:
------------
As usual this sketch consist of a setup() and a loop() function.


Helper classes:
---------------
The purpose of separate (helper) classes is to make the main sketch more readable and to
make the (helper) classes available for other sketches, possibly on other (Arduino-like) platforms.
These classes are:
- Flasher:      Class to define a led connected to the board an be able to let it flash.
                The on-period and off-period are set in the constructor of the class.
- Flasheronce:  Class to define a led connected to the board an be able to let it flash once.
                The on-period is set in the constructor of the class.
- Toggler:      Class to define a led connected to the board an be able to turn this led on and off (as
                a flipflop).
- Toucher:      CURRENTLY NOT USED IN THIS SKETCH.
                Class to read a button connected to the board. When the button is pressed this can be
                detected by the main sketch. Its status remains "pressed" untill it is read by the main
                sketch. This to prevent that the main sketch misses a button push.
                The button sample rate is set in the constructor of the class but can never be less than
                the speed of the loop() in the main sketch.

Messages:
---------
The sensor is constructed to send 2 types of messages:
1.  Alive messages
    This is also known as Heartbeat message. Such a mesage is send at regular intervals to inform
    the backend that the sensor module is still operational.
    At his moment an interval of 30 seconds is used.
    The Alive message also contains:
    - The (2 digits) temperature as read form the connected DHT sensor. Once in a while the DHT.h/DHT.cpp library
      returns a misreading in the form of "NAN" (Not A Number).
2.  Event messages
    These are send when the microswitch connected to the Mbili is opened ("1") or closed("0").
    
       
      



