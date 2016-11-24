/**
 * receive TTN messages and export them to RabbitMQ
 *
 * 2016-10-11 Ab Reitsma
 */
"use strict";
var winston = require("winston");
var decodeProtobuf_1 = require("./decodeProtobuf");
var ReceiveTTN = (function () {
    function ReceiveTTN(ttnMQTT, sender) {
        var _this = this;
        this.mqttClient = ttnMQTT;
        this.sender = sender;
        // initialize MQTT message receive
        ttnMQTT.on("connect", function () {
            winston.info("Connected to the TTN MQTT exchange.");
            ttnMQTT.subscribe("#");
        });
        ttnMQTT.on("message", function (topic, message) {
            winston.debug("TTN message received.", message);
            _this.messageConsumerMQTT(topic, message);
        });
    }
    ReceiveTTN.prototype.messageConsumerMQTT = function (topic, messageRaw) {
        var messageTTN = JSON.parse(messageRaw.toString());
        var rawPayload = new Buffer(messageTTN.payload, "base64");
        var payload = decodeProtobuf_1.default(rawPayload);
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
    return ReceiveTTN;
}());
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = ReceiveTTN;

//# sourceMappingURL=receiveTTN.js.map
