/**
 * receive TTN messages and export them to RabbitMQ
 *
 * 2016-10-11 Ab Reitsma
 */
"use strict";
var decodeProtoBuf_1 = require("./decodeProtoBuf");
var ReceiveKPN = (function () {
    function ReceiveKPN(receiver, sender) {
        var _this = this;
        this.receiver = receiver;
        this.sender = sender;
        receiver.startConsumer(function (msg) {
            _this.MessageConsumerKPN(msg);
        });
    }
    ReceiveKPN.prototype.MessageConsumerKPN = function (msg) {
        try {
            var rawPayload = new Buffer(msg.payload_hex, "hex");
            var payload = decodeProtoBuf_1.default(rawPayload);
            var metadata = {
                server_time: new Date(msg.Time).toISOString(),
                longitude: Number(msg.LrrLON),
                latitude: Number(msg.LrrLAT)
            };
            // convert payload
            var messageIot = {
                payload: payload,
                port: msg.FPort,
                counter: msg.FCntUp,
                dev_eui: msg.DevEUI,
                metadata: metadata
            };
            // publish result
            this.sender.send(messageIot);
        }
        catch (err) {
            console.log(err);
        }
    };
    return ReceiveKPN;
}());
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = ReceiveKPN;

//# sourceMappingURL=receiveKPN.js.map
