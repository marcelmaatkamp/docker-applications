/**
 * process slack notification
 *
 * 2016-10-18 Ab Reitsma
 */

import * as iot from "./iotMsg";
import * as Slack from "node-slack";

export default class ProcessNotificationSlack {
  receiver: iot.ReceiveMessages;
  sender: iot.SendMessages;
  slack: Slack;

  constructor(receiver: iot.ReceiveMessages, sender: iot.SendMessages | null, slack: Slack) {
    this.receiver = receiver;
    this.sender = sender;
    this.slack = slack;

    receiver.startConsumer((msg) => {
      this.processNotification(msg);
    });
  }

  private processNotification(notification: iot.AlertNotification) {
    // haal alarmregels
    if(notification.kanaal.toLowerCase() === "slack") {
        this.sendNotification(notification);
      }
  }

  private sendNotification(notification: iot.AlertNotification) {
    var message = {
      text: notification.meldingtekst,
      channel: notification.p1,
      username: notification.p2 || "Sensormelding"
    };
    this.slack.send(message);
    if (this.sender) {
      this.sender.send(message);
    }
  }
}
