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
        var queryString = "SELECT kanaal, p1, p2, p3, p4, meldingtekst FROM alarm_notificatie " +
            "WHERE alarm_regel = " + alert.ruleId + ";";
        this.sqlConnection.query(queryString, function (err, results) {
            if (err) {
                //todo: log sql error
                console.log(queryString);
                console.log(err);
            }
            else {
                console.log("----------------");
                console.log(queryString);
                console.log(results);
                _this.checkNotifications(alert, results);
            }
        });
    };
    ProcessAlert.prototype.checkNotifications = function (alert, notifications) {
        for (var i = 0, len = notifications.length; i < len; i++) {
            notifications[i].meldingtekst = notifications[i].meldingtekst
                .replace("%v", alert.sensorValue)
                .replace("%t", alert.sensorValueType);
            this.sender.send(notifications[i]);
        }
    };
    return ProcessAlert;
}());
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = ProcessAlert;

//# sourceMappingURL=processAlert.js.map
