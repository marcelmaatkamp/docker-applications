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

  constructor(receiver: iot.ReceiveMessages, sender: iot.SendMessages | null, sqlConnection: mysql.IConnection) {
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
      "INSERT INTO alarm ( " +
      "alarm_regel," +
      "observatie) " +
      "VALUES (" +
      alert.ruleId + "," +
      alert.observationId +
      ");";
    this.sqlConnection.query(queryString, (err, results) => {
      if (err) {
        //todo: log sql error
        console.log(queryString);
        console.log(err);
      } else {
        if (this.sender) {
          try {
            alert.logId = results.insertId;
            this.sender.send(alert);
          } catch (err) {
            //todo: log error
            console.log(err);
          }
        }
      }
    });
  }
}
