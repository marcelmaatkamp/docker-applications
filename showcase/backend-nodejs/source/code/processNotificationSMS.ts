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
  fromPhone: string;

  constructor(receiver: iot.ReceiveMessages, sender: iot.SendMessages | null, client: any, fromPhone: string) {
    this.receiver = receiver;
    this.sender = sender;
    this.client = client;
    this.fromPhone = fromPhone;

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
    this.client.sendMessage({
      to: notification.p1,
      from: this.fromPhone,
      body: notification.meldingtekst,
    }, (err, responseData) => {
      if (err) {
        winston.error("error sending SMS message with twilio: " + err.message, err);
      } else {
        winston.info("Message sent to SMS.", responseData);
        if (this.sender) {
          this.sender.send({
            to: notification.p1,
            from: this.fromPhone,
            body: notification.meldingtekst,
          });
        }
      }
    });
  }
}
