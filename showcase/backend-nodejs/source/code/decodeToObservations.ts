/**
 * receive messages and decode them into observations
 *
 * 2016-10-11 Ab Reitsma
 */

import * as winston from "winston";
import * as iot from "./iotMsg";
import * as mysql from "mysql";

var safeEval = require("safe-eval");

var messageCounters: { [nodeId: string]: number } = {};

export default class DecodeToObservations {
  receiver: iot.ReceiveMessages;
  sender: iot.SendMessages;
  sqlConnection: mysql.IConnection;

  constructor(receiver: iot.ReceiveMessages, sender: iot.SendMessages, sqlConnection: mysql.IConnection) {
    this.receiver = receiver;
    this.sender = sender;
    this.sqlConnection = sqlConnection;

    receiver.startConsumer((msg) => {
      this.messageConsumerDecode(msg);
    });
  }

  /**
   * adds the type of the sensor and converts the sensor value if needed
   * before sending the sensor to the destination exchange
   */
  private sendCompletedObservation(observation: iot.SensorObservation) {
    var queryString = "SELECT omrekenfactor,eenheid" +
      " FROM sensor WHERE" +
      " sensor_id = " + observation.sensorId + ";";
    this.sqlConnection.query(queryString, (err, results) => {
      if (err) {
        winston.error("Error executing sql query: " + err, queryString);
      } else {
        try {
          var omrekenfactor = results[0].omrekenfactor;
          observation.sensorValueType = results[0].eenheid;
          if (omrekenfactor) {
            var context = {
              x: observation.sensorValue,
              X: observation.sensorValue
            };
            observation.sensorValue = safeEval(omrekenfactor, context);
          }
          this.sender.send(observation);
        } catch (err) {
          winston.error("Error completing observation: " + err.message, err);
        }
      }
    });
  }

  private messageConsumerDecode(message: iot.Message) {
    try {
      // decode all readings to observations

      // expect ttnMsg to have a reading property that contains an array of objects
      // each object contains the properties defined in sensor.proto SensorReading
      var payload = message.payload;
      var nodeId = message.dev_eui;
      var timestamp = message.metadata[0].server_time;

      // send all sensor values as separate msg's
      for (let len = payload.length, i = 0; i < len; i++) {
        let observation: iot.SensorObservation = {
          nodeId: nodeId,
          sensorId: payload[i].id,
          sensorValue: payload[i].value1,
          sensorError: payload[i].error,
          timestamp: timestamp
        };
        this.sendCompletedObservation(observation);
      }

      // check for skipped messages
      var currentCount = message.counter;
      var lastCount = messageCounters[nodeId] || currentCount - 1;
      var skippedCount = currentCount - lastCount - 1;
      // node.warn("Message count: " + currentCount + " previous count: " + lastCount + " skipped: " + skippedCount);
      if (skippedCount) {
        let observation: iot.SensorObservation = {
          nodeId: nodeId,
          sensorId: -1,
          sensorValue: skippedCount,
          sensorValueType: "skipped",
          sensorError: 0,
          timestamp: timestamp
        };
        this.sender.send(observation);
      }
      messageCounters[nodeId] = currentCount;

      // always notify that node is still alive (in a clean msg)
      let observation: iot.SensorObservation = {
        nodeId: nodeId,
        sensorId: 0,
        sensorValue: message.metadata[0].rssi,
        sensorValueType: "db",
        sensorError: 0,
        timestamp: timestamp
      };
      this.sender.send(observation);
    } catch (err) {
      winston.error("Error decoding message: " + err.message, err);
    }
  }
}
