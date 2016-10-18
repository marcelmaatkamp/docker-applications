/**
 * receive messages and decode them into observations
 *
 * 2016-10-11 Ab Reitsma
 */

import * as iot from "./iotMsg";
import * as mysql from "mysql";

export default class LogObservation {
  receiver: iot.ReceiveMessages;
  sender: iot.SendMessages;
  sqlConnection: mysql.IConnection;

  constructor(receiver: iot.ReceiveMessages, sender: iot.SendMessages, sqlConnection: mysql.IConnection) {
    this.receiver = receiver;
    this.sender = sender;
    this.sqlConnection = sqlConnection;

    receiver.startConsumer((msg) => {
      this.SendLoggedObservation(msg);
    });
  }

  /**
   * adds the type of the sensor and converts the sensor value if needed
   * before sending the sensor to the destination exchange
   */
  private SendLoggedObservation(observation: iot.SensorObservation) {
    var observationTimestamp = observation.timestamp.slice(0, 19).replace('T', ' ');
    var nodeId = observation.nodeId;
    var sensorId = observation.sensorId;
    var sensorValue = observation.sensorValue;

    var queryString =
      "INSERT INTO observatie ( " +
      "datum_tijd_aangemaakt," +
      "node," +
      "sensor," +
      "waarde) " +
      "VALUES (" +
      "'" + observationTimestamp + "'," +
      "'" + nodeId + "'," +
      sensorId + ", " +
      "'" + sensorValue + "'" +
      ");";
    this.sqlConnection.query(queryString, (err, results) => {
      if (err) {
        //todo: log sql error
        console.log(queryString);
        console.log(err);
      } else {
        try {
          observation.logId = results.insertId;
          this.sender.send(observation);
        } catch (err) {
          //todo: log error
          console.log(err);
        }
      }
    });
  }
}
