package showcase

import showcase.sensor.Sensor
import showcase.MyReflectionToStringBuilder

class Persoon { 

 String voornaam
 String achternaam
 String functie 
 String email
 String telnr
 
 static hasMany = [sensor:Sensor]
 static belongsTo = showcase.sensor.Sensor

 static constraints = {
    achternaam()
    voornaam()
    functie()
    telnr size:10..12
    email blank: false, email: true
 }

 String toString() {
    // return new MyReflectionToStringBuilder(this).toString();
    return (achternaam ? achternaam.toUpperCase() + ", " : "") + voornaam
 }

}
