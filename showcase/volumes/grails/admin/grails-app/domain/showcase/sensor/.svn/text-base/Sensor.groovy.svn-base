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

 static hasMany = [events: Event, alert: Persoon]

 static constraints = {
    identifier()
    sensorType()
    omschrijving()
    slackchannel()
    telegramchannel()
    events()
 }

 static mapping = {
   events sort:'dateCreated' // order:'desc'
 }

 String toString() {
    return identifier+","+sensorType
 }

}

