import showcase.*
import showcase.event.*
import showcase.sensor.*

class BootStrap {

    def saveToDatabase = false;

    def lora = new SensorType(type: "lora");
    def robert = new Persoon( voornaam:"Robert", achternaam:"Robert", functie:"dba", email:"robert.kuipers@pirod.nl", telnr:"+316-ROBERT");
    def marcel = new Persoon( voornaam:"Marcel", achternaam:"Maatkamp", functie:"ontwikkelaar", email:"marcel.maatkamp@pirod.nl", telnr:"+316-MARCEL");

    def alarm = new EventType(type: EventType.Type.ALARM);
    def keepalive = new EventType(type: EventType.Type.KEEP_ALIVE);

    def nexus = new Sensor( sensorType: lora, identifier:"000000009AA74038", omschrijving: "koffer met nexus", slackchannel:"koffer_1", telegramchannel: "-1001050387961");
    def mbili = new Sensor( sensorType: lora, identifier:"0000000029B1A8A0", omschrijving: "koffer met mbili", slackchannel:"koffer_2", telegramchannel: "-1001052211267");

    def tempEvent = new TemperatureEvent(sensor: nexus, ontvangstdatum: new Date(), eventType: keepalive, temperature: 23.3);
    def switchEvent = new SwitchEvent(sensor: nexus, ontvangstdatum: new Date(), eventType: alarm, status: SwitchEvent.Status.OPEN);
  
    def init = { servletContext ->
     TimeZone.setDefault(TimeZone.getTimeZone("UTC"))
     if(saveToDatabase) { 
       lora.save();
       robert.save();
       marcel.save();
       alarm.save();
       keepalive.save();
       nexus.save();
       nexus.addToAlert(marcel);
       mbili.save();
       mbili.addToAlert(robert);
       nexus.addToAlert(marcel);
       tempEvent.save();
       switchEvent.save();
     }
   }

   def destroy = {

   }
}
