/**
 * process slack notification
 *
 * 2016-10-18 Ab Reitsma
 */
"use strict";
var winston = require("winston");
var ProcessNotificationSMS = (function () {
    function ProcessNotificationSMS(receiver, sender, client, fromPhone) {
        var _this = this;
        this.receiver = receiver;
        this.sender = sender;
        this.client = client;
        this.fromPhone = fromPhone;
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
        this.client.sendMessage({
            to: notification.p1,
            from: this.fromPhone,
            body: notification.meldingtekst,
        }, function (err, responseData) {
            if (err) {
                winston.error("error sending SMS message with twilio: " + err.message, err);
            }
            else {
                winston.info("Message sent to SMS.", responseData);
                if (_this.sender) {
                    _this.sender.send({
                        to: notification.p1,
                        from: _this.fromPhone,
                        body: notification.meldingtekst,
                    });
                }
            }
        });
    };
    return ProcessNotificationSMS;
}());
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = ProcessNotificationSMS;

//# sourceMappingURL=processNotificationSms.js.map
