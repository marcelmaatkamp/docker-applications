/**
 * process slack notification
 *
 * 2016-10-18 Ab Reitsma
 */

import * as winston from "winston";
import * as iot from "./iotMsg";

export default class ProcessNotificationSMS {
  receiver: iot.ReceiveMessages;
  sender: iot.SendMessages;  
  client: any; 

  constructor(receiver: iot.ReceiveMessages, sender: iot.SendMessages | null) {
    this.receiver = receiver;
    this.sender = sender;

    var accountSid = 'AC600a293801150c7c3af3a5747a3ba4ae';
    var authToken = 'ad1f82c56f5b9f048e72558ae984edf8';
    this.client = require('twilio')(accountSid, authToken);

    receiver.startConsumer((msg) => {
      this.processNotification(msg);
    });
  }

  private processNotification(notification: iot.AlertNotification) {
    // haal alarmregels
    if (notification.kanaal.toLowerCase() === "sms") {
      this.sendNotification(notification);
    }
  }

  private sendNotification(notification: iot.AlertNotification) {


    this.client.messages.create({
        to:   notification.p1,
        from: "+19787124065",
        body:  notification.meldingtekst,
    }, function(err, message) {
        console.log("error: " + JSON.stringify(err) + ", " + JSON.stringify(message));
    }).then(() => {
        winston.info("Message sent to SMS.");
        if (this.sender) {
          this.sender.send(notification.meldingtekst);
        }
      })
      .catch((err) => {
        winston.error("error sending telegram message: " + err.message, err);
      });
  }
}
