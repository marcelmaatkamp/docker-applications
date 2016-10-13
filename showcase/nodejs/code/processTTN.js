/**
 * receive TTN messages and export them to RabbitMQ
 *
 * 2016-10-11 Ab Reitsma
 */
"use strict";
var amqp = require("amqp-ts");
var decodeProtoBuf_1 = require("./decodeProtoBuf");
var ProcessTTN = (function () {
    function ProcessTTN(ttnMQTT, destinationAMQP) {
        this.mqttClient = ttnMQTT;
        this.amqpExchange = destinationAMQP;
        // initialize MQTT message receive
        ttnMQTT.on("connect", function () {
            ttnMQTT.subscribe("#");
        });
        ttnMQTT.on("message", this.MessageConsumerMQTT);
    }
    ProcessTTN.prototype.MessageConsumerMQTT = function (topic, messageRaw) {
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
        // publish to exchange
        var messageAmqp = new amqp.Message(messageIot);
        this.amqpExchange.send(messageAmqp);
    };
    return ProcessTTN;
}());
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = ProcessTTN;

//# sourceMappingURL=processTTN.js.map
