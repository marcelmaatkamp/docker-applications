/**
 * log observations into the mysql database
 * add the log index to the observation and forward it
 *
 * 2016-10-18 Ab Reitsma
 */
"use strict";
var winston = require("winston");
var LogObservation = (function () {
    function LogObservation(receiver, sender, sqlConnection) {
        var _this = this;
        this.receiver = receiver;
        this.sender = sender;
        this.sqlConnection = sqlConnection;
        receiver.startConsumer(function (msg) {
            _this.logObservation(msg);
        });
    }
    /**
     * adds the type of the sensor and converts the sensor value if needed
     * before sending the sensor to the destination exchange
     */
    LogObservation.prototype.logObservation = function (observation) {
        var _this = this;
        var observationTimestamp = observation.timestamp.slice(0, 19).replace('T', ' ');
        var nodeId = observation.nodeId;
        var sensorId = observation.sensorId;
        var sensorValue = observation.sensorValue;
        var queryString = "INSERT INTO observatie ( " +
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
        this.sqlConnection.query(queryString, function (err, results) {
            if (err) {
                winston.error("Error executing sql query: " + err, queryString);
            }
            else {
                try {
                    observation.logId = results.insertId;
                    _this.sender.send(observation);
                }
                catch (err) {
                    winston.error("Error logging observation: " + err.message, err);
                }
            }
        });
    };
    return LogObservation;
}());
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = LogObservation;

//# sourceMappingURL=logObservation.js.map
