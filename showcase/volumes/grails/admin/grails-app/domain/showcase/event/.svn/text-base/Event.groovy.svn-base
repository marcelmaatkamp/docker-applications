package showcase.event

import showcase.sensor.Sensor
import showcase.MyReflectionToStringBuilder

class Event {

  Date dateCreated
  EventType eventType

  static belongsTo = [sensor: Sensor]

  static constraints = {
    dateCreated()
    eventType()
    sensor()
  }

  static mapping = {
    sort dateCreated: "desc"
  }

 String toString() {
    return dateCreated.toString() + "," + eventType.toString() 
  }

}
