/**
 * process slack notification
 *
 * 2016-10-18 Ab Reitsma
 */
"use strict";
var winston = require("winston");
var ProcessNotificationSlack = (function () {
    function ProcessNotificationSlack(receiver, sender, slack) {
        var _this = this;
        this.receiver = receiver;
        this.sender = sender;
        this.slack = slack;
        receiver.startConsumer(function (msg) {
            _this.processNotification(msg);
        });
    }
    ProcessNotificationSlack.prototype.processNotification = function (notification) {
        // haal alarmregels
        if (notification.kanaal.toLowerCase() === "slack") {
            this.sendNotification(notification);
        }
    };
    ProcessNotificationSlack.prototype.sendNotification = function (notification) {
        var message = {
            text: notification.meldingtekst,
            channel: notification.p1,
            username: notification.p2 || "Sensormelding"
        };
        this.slack.send(message);
        winston.info("Message sent to Slack.");
        if (this.sender) {
            this.sender.send(message);
        }
    };
    return ProcessNotificationSlack;
}());
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = ProcessNotificationSlack;

//# sourceMappingURL=processNotificationSlack.js.map
