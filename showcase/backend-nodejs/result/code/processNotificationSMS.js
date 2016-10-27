/**
 * process slack notification
 *
 * 2016-10-18 Ab Reitsma
 */
"use strict";
var winston = require("winston");
var ProcessNotificationSMS = (function () {
    function ProcessNotificationSMS(receiver, sender) {
        var _this = this;
        this.receiver = receiver;
        this.sender = sender;
        var accountSid = 'AC600a293801150c7c3af3a5747a3ba4ae';
        var authToken = 'ad1f82c56f5b9f048e72558ae984edf8';
        this.client = require('twilio')(accountSid, authToken);
        receiver.startConsumer(function (msg) {
            _this.processNotification(msg);
        });
    }
    ProcessNotificationSMS.prototype.processNotification = function (notification) {
        // haal alarmregels
        if (notification.kanaal.toLowerCase() === "sms") {
            this.sendNotification(notification);
        }
    };
    ProcessNotificationSMS.prototype.sendNotification = function (notification) {
        var _this = this;
        this.client.messages.create({
            to: notification.p1,
            from: "+19787124065",
            body: notification.meldingtekst,
        }, function (err, message) {
            console.log("error: " + JSON.stringify(err) + ", " + JSON.stringify(message));
        }).then(function () {
            winston.info("Message sent to SMS.");
            if (_this.sender) {
                _this.sender.send(notification.meldingtekst);
            }
        })
            .catch(function (err) {
            winston.error("error sending telegram message: " + err.message, err);
        });
    };
    return ProcessNotificationSMS;
}());
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = ProcessNotificationSMS;

//# sourceMappingURL=processNotificationSMS.js.map
