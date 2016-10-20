/**
 * process alerts
 *
 * 2016-10-18 Ab Reitsma
 */

import * as iot from "./iotMsg";
import * as mysql from "mysql";

export default class ProcessAlert {
  receiver: iot.ReceiveMessages;
  sender: iot.SendMessages;
  sqlConnection: mysql.IConnection;

  constructor(receiver: iot.ReceiveMessages, sender: iot.SendMessages, sqlConnection: mysql.IConnection) {
    this.receiver = receiver;
    this.sender = sender;
    this.sqlConnection = sqlConnection;

    receiver.startConsumer((msg) => {
      this.processAlert(msg);
    });
  }

  private processAlert(alert: iot.SensorAlert) {
    // haal alarmregels
    var queryString =
      "SELECT kanaal, p1, p2, p3, p4, meldingtekst FROM alarm_notificatie " +
      "WHERE alarm_regel = " + alert.ruleId + ";";
    this.sqlConnection.query(queryString, (err, results) => {
      if (err) {
        //todo: log sql error
        console.log(queryString);
        console.log(err);
      } else {
        this.sendNotifications(alert, results);
      }
    });
  }

  private sendNotifications(alert: iot.SensorAlert, notifications: any[]) {
    for (var i = 0, len = notifications.length; i < len; i++) {
      notifications[i].meldingtekst = notifications[i].meldingtekst
        .replace("%v", alert.sensorValue)
        .replace("%t", alert.sensorValueType);

      this.sender.send(notifications[i]);
    }
  }
}
