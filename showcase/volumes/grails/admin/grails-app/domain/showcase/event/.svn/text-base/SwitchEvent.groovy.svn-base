package showcase.event

import showcase.sensor.Sensor
import showcase.MyReflectionToStringBuilder

class SwitchEvent extends Event {
  enum Status {
    OPEN,CLOSED
  }

  Status status

  static constraints = {
    status blank: false
  }

 String toString() {
    return super.toString() + ", status="+status
 }

}
