package showcase.sensor

import showcase.Persoon
import showcase.sensor.Sensor
import showcase.event.Event
import showcase.MyReflectionToStringBuilder

class Sensor {

 SensorType sensorType
 String identifier
 String omschrijving
 String slackchannel
 String telegramchannel
 String mintemp
 String maxtemp
 String minvolt
 String maxvolt
 String maxhumidity
 String minhumidity
 String keepalivetimeout

 static hasMany = [events: Event, alert: Persoon]

 static constraints = {
    identifier()
    sensorType()
    omschrijving()
    slackchannel()
    telegramchannel()
    mintemp()
    maxtemp()
    minvolt()
    maxvolt()
    minhumidity()
    maxhumidity()
    keepalivetimeout()
    events() 
}

 static mapping = {
   events sort:'dateCreated' // order:'desc'
 }

 String toString() {
    return identifier+","+sensorType
 }

}

