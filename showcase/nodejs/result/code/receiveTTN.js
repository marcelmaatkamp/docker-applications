/**
 * receive TTN messages and export them to RabbitMQ
 *
 * 2016-10-11 Ab Reitsma
 */
"use strict";
var decodeProtoBuf_1 = require("./decodeProtoBuf");
var ReceiveKPN = (function () {
    function ReceiveKPN(ttnMQTT, sender) {
        var _this = this;
        this.mqttClient = ttnMQTT;
        this.sender = sender;
        // initialize MQTT message receive
        ttnMQTT.on("connect", function () {
            ttnMQTT.subscribe("#");
        });
        ttnMQTT.on("message", function (topic, message) {
            _this.MessageConsumerMQTT(topic, message);
        });
    }
    ReceiveKPN.prototype.MessageConsumerMQTT = function (topic, messageRaw) {
        var messageTTN = JSON.parse(messageRaw.toString());
        var rawPayload = new Buffer(messageTTN.payload, "base64");
        var payload = decodeProtoBuf_1.default(rawPayload);
        // convert payload
        var messageIot = {
            payload: payload,
            port: messageTTN.port,
            counter: messageTTN.counter,
            dev_eui: messageTTN.dev_eui,
            metadata: messageTTN.metadata
        };
        // publish
        this.sender.send(messageIot);
    };
    return ReceiveKPN;
}());
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = ReceiveKPN;

//# sourceMappingURL=receiveTTN.js.map
