/**
 * process observations
 *
 * 2016-10-18 Ab Reitsma
 */

import * as iot from "./iotMsg";
import * as mysql from "mysql";

var safeEval = require("safe-eval");

var heartbeatTimers: { [nodeId: string]: NodeJS.Timer } = {};

export default class ProcessObservation {
  receiver: iot.ReceiveMessages;
  sender: iot.SendMessages;
  sqlConnection: mysql.IConnection;

  constructor(receiver: iot.ReceiveMessages, sender: iot.SendMessages, sqlConnection: mysql.IConnection) {
    this.receiver = receiver;
    this.sender = sender;
    this.sqlConnection = sqlConnection;

    receiver.startConsumer((msg) => {
      this.processObservation(msg);
    });
  }


  private processObservation(observation: iot.SensorObservation) {
    if (observation.sensorError) {
      return; // do not process  sensor observations with errors (for now)
    }
    // haal alarmregels
    var queryString =
      "SELECT id, alarm_trigger, omrekenfactor" +
      " FROM alarm_regel, sensor WHERE" +
      " alarm_regel.sensor = sensor.sensor_id AND" +
      " node = '" + observation.nodeId + "'" +
      " AND" +
      " sensor = " + observation.sensorId + ";";
    this.sqlConnection.query(queryString, (err, results) => {
      if (err) {
        //todo: log sql error
        console.log(queryString);
        console.log(err);
      } else {
        console.log("------------------");
        console.log(results);
        this.checkRules(observation, results);
      }
    });
  }

  private checkRules(observation: iot.SensorObservation, rules: any[]) {
    if (observation.sensorId === 0) {
      // sensor 0 is the heartbeat, setup timeout(s)
      for (var len = rules.length, i = 0; i < len; i++) {
        // stop running timer
        var timer = heartbeatTimers[rules[i].id];
        // reset heartbeat timeout timer
        if (timer) {
          clearTimeout(timer);
        }
        var timeoutSeconds = Number(rules[i].alarm_trigger);
        if (!isNaN(timeoutSeconds)) {
          timer = setTimeout((rule) => {
            delete heartbeatTimers[rule.id];
            this.sendAlert(observation, rule);
          }, timeoutSeconds * 1000, rules[i]);
          heartbeatTimers[rules[i].id] = timer;
        }
      }
    } else if (!observation.sensorError) {
      // evaluate alarm_trigger(s)
      for (var len = rules.length, i = 0; i < len; i++) {
        try {
          var evalContext = {
            X: observation.sensorValue,
            x: observation.sensorValue
          };
          var result = safeEval(rules[i].alarm_trigger, evalContext);
          // node.warn("rule evaluation for rule " + rules[i].id +
          //     " on state '" + rules[i].alarm_trigger + "', with x = " + observation.sensorValue + " returns " + result);
          if (result) {
            this.sendAlert(observation, rules[i]);
          }
        } catch (err) {
          // todo: log rule evaluation error
          console.log("rule evaluation error for rule " + rules[i].id +
            " on state '" + rules[i].alarm_trigger + "', with x = " + observation.sensorValue);
        }
      }
    }
  }

  private sendAlert(observation: iot.SensorObservation, rule) {
    var alertMsg = {
      nodeId: observation.nodeId,
      sensorId: observation.sensorId,
      sensorValue: observation.sensorValue,
      observationId: observation.logId,
      ruleId: rule.id
    };
    this.sender.send(alertMsg);

  }
}
