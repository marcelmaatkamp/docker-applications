/**
 * receive TTN messages and export them to RabbitMQ
 *
 * 2016-10-11 Ab Reitsma
 */
"use strict";
var winston = require("winston");
var decodeProtobuf_1 = require("./decodeProtobuf");
var ReceiveKPN = (function () {
    function ReceiveKPN(receiver, sender) {
        var _this = this;
        this.receiver = receiver;
        this.sender = sender;
        receiver.startConsumer(function (msg) {
            _this.messageConsumerKPN(msg);
        });
    }
    ReceiveKPN.prototype.messageConsumerKPN = function (msg) {
        try {
            var rawPayload = new Buffer(msg.payload_hex, "hex");
            var payload = decodeProtobuf_1.default(rawPayload);
            var metadata = [{
                    rssi: Number(msg.LrrRSSI),
                    server_time: new Date(msg.Time).toISOString(),
                    longitude: Number(msg.LrrLON),
                    latitude: Number(msg.LrrLAT)
                }];
            // convert payload
            var messageIot = {
                payload: payload,
                port: msg.FPort,
                counter: msg.FCntUp,
                dev_eui: msg.DevEUI,
                metadata: metadata
            };
            // publish result
            winston.info("Message received from KPN.");
            this.sender.send(messageIot);
        }
        catch (err) {
            winston.error("Error receiving KPN message: " + err.message, err);
        }
    };
    return ReceiveKPN;
}());
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = ReceiveKPN;

//# sourceMappingURL=receiveKPN.js.map
