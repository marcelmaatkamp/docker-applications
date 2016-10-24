/**
 * log observations into the mysql database
 * add the log index to the observation and forward it
 *
 * 2016-10-18 Ab Reitsma
 */

import * as winston from "winston";
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
      this.logObservation(msg);
    });
  }

  /**
   * adds the type of the sensor and converts the sensor value if needed
   * before sending the sensor to the destination exchange
   */
  private logObservation(observation: iot.SensorObservation) {
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
        winston.error("Error executing sql query: " + err, queryString);
      } else {
        try {
          observation.logId = results.insertId;
          this.sender.send(observation);
        } catch (err) {
          winston.error("Error logging observation: " + err.message, err);
        }
      }
    });
  }
}
