package showcase.event

import showcase.MyReflectionToStringBuilder

class EventType {

  public enum Type {
    KEEP_ALIVE, ALARM
  }

  Type type

  static constraints = {
    type blank: false
  }

 String toString() {
    return type
 }

}
