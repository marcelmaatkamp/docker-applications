/**
 * receive messages and decode them into observations
 *
 * 2016-10-11 Ab Reitsma
 */
"use strict";
var safeEval = require("safe-eval");
var messageCounters = {};
var DecodeToObservations = (function () {
    function DecodeToObservations(receiver, sender, sqlConnection) {
        var _this = this;
        this.receiver = receiver;
        this.sender = sender;
        this.sqlConnection = sqlConnection;
        receiver.startConsumer(function (msg) {
            _this.messageConsumerDecode(msg);
        });
    }
    /**
     * adds the type of the sensor and converts the sensor value if needed
     * before sending the sensor to the destination exchange
     */
    DecodeToObservations.prototype.sendCompletedObservation = function (observation) {
        var _this = this;
        var queryString = "SELECT omrekenfactor,eenheid" +
            " FROM sensor WHERE" +
            " sensor_id = " + observation.sensorId + ";";
        this.sqlConnection.query(queryString, function (err, results) {
            if (err) {
                //todo: log sql error
                console.log(queryString);
                console.log(err);
            }
            else {
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
                    _this.sender.send(observation);
                }
                catch (err) {
                    //todo: log error
                    console.log(err);
                }
            }
        });
    };
    DecodeToObservations.prototype.messageConsumerDecode = function (message) {
        try {
            // decode all readings to observations
            // expect ttnMsg to have a reading property that contains an array of objects
            // each object contains the properties defined in sensor.proto SensorReading
            var payload = message.payload;
            var nodeId = message.dev_eui;
            var timestamp = message.metadata.server_time;
            // send all sensor values as separate msg's
            for (var len = payload.length, i = 0; i < len; i++) {
                var observation_1 = {
                    nodeId: nodeId,
                    sensorId: payload[i].id,
                    sensorValue: payload[i].value1,
                    sensorError: payload[i].error,
                    timestamp: timestamp
                };
                this.sendCompletedObservation(observation_1);
            }
            // check for skipped messages
            var currentCount = message.counter;
            var lastCount = messageCounters[nodeId] || currentCount - 1;
            var skippedCount = currentCount - lastCount - 1;
            // node.warn("Message count: " + currentCount + " previous count: " + lastCount + " skipped: " + skippedCount);
            if (skippedCount) {
                var observation_2 = {
                    nodeId: nodeId,
                    sensorId: -1,
                    sensorValue: skippedCount,
                    sensorValueType: "skipped",
                    sensorError: 0,
                    timestamp: timestamp
                };
                this.sender.send(observation_2);
            }
            messageCounters[nodeId] = currentCount;
            // always notify that node is still alive (in a clean msg)
            var observation = {
                nodeId: nodeId,
                sensorId: 0,
                sensorValue: 1,
                sensorValueType: "alive",
                sensorError: 0,
                timestamp: timestamp
            };
            this.sender.send(observation);
        }
        catch (err) {
            console.log(err);
        }
    };
    return DecodeToObservations;
}());
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = DecodeToObservations;

//# sourceMappingURL=decodeToObservations.js.map
