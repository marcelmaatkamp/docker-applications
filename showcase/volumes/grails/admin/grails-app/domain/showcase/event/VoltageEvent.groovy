package showcase.event

import showcase.sensor.Sensor
import showcase.MyReflectionToStringBuilder

class VoltageEvent extends Event {
  double voltage

 String toString() {
    return super.toString() + ", voltage="+voltage
  }
}
