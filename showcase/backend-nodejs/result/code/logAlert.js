/**
 * process alerts
 *
 * 2016-10-18 Ab Reitsma
 */
"use strict";
var ProcessAlert = (function () {
    function ProcessAlert(receiver, sender, sqlConnection) {
        var _this = this;
        this.receiver = receiver;
        this.sender = sender;
        this.sqlConnection = sqlConnection;
        receiver.startConsumer(function (msg) {
            _this.processAlert(msg);
        });
    }
    ProcessAlert.prototype.processAlert = function (alert) {
        var _this = this;
        // haal alarmregels
        var queryString = "INSERT INTO alarm ( " +
            "alarm_regel," +
            "observatie) " +
            "VALUES (" +
            alert.ruleId + "," +
            alert.observationId +
            ");";
        this.sqlConnection.query(queryString, function (err, results) {
            if (err) {
                //todo: log sql error
                console.log(queryString);
                console.log(err);
            }
            else {
                if (_this.sender) {
                    try {
                        alert.logId = results.insertId;
                        _this.sender.send(alert);
                    }
                    catch (err) {
                        //todo: log error
                        console.log(err);
                    }
                }
            }
        });
    };
    return ProcessAlert;
}());
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = ProcessAlert;

//# sourceMappingURL=logAlert.js.map
