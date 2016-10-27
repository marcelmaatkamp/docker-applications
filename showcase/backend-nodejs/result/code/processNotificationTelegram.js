/**
 * process slack notification
 *
 * 2016-10-18 Ab Reitsma
 */
"use strict";
var winston = require("winston");
var ProcessNotificationTelegram = (function () {
    function ProcessNotificationTelegram(receiver, sender, telegramBot) {
        var _this = this;
        this.receiver = receiver;
        this.sender = sender;
        this.telegramBot = telegramBot;
        receiver.startConsumer(function (msg) {
            _this.processNotification(msg);
        });
    }
    ProcessNotificationTelegram.prototype.processNotification = function (notification) {
        // haal alarmregels
        if (notification.kanaal.toLowerCase() === "telegram") {
            this.sendNotification(notification);
        }
    };
    ProcessNotificationTelegram.prototype.sendNotification = function (notification) {
        var _this = this;
        var message = {
            text: notification.meldingtekst,
            chatId: notification.p1 || process.env.SHOWCASE_TELEGRAM_CHAT_ID || "-1001097331998",
        };
        this.telegramBot.sendMessage(message.chatId, message.text)
            .then(function () {
            winston.info("Message sent to Telegram.");
            if (_this.sender) {
                _this.sender.send(message);
            }
        })
            .catch(function (err) {
            winston.error("error sending telegram message: " + err.message, err);
        });
    };
    return ProcessNotificationTelegram;
}());
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = ProcessNotificationTelegram;

//# sourceMappingURL=processNotificationTelegram.js.map
