/**
 * receive TTN messages and export them to RabbitMQ
 *
 * 2016-10-11 Ab Reitsma
 */
"use strict";
var amqp = require("amqp-ts");
var decodeProtoBuf_1 = require("./decodeProtoBuf");
var ProcessKPN = (function () {
    function ProcessKPN(srcQueue, destExchange) {
        this.srcQueue = srcQueue;
        this.destExchange = destExchange;
        srcQueue.activateConsumer(this.MessageConsumerKPN, { noAck: true });
    }
    ProcessKPN.prototype.MessageConsumerKPN = function (msg) {
        var messageRaw = msg.content;
        var messageKPN = JSON.parse(messageRaw.toString());
        var rawPayload = new Buffer(messageKPN.payload_hex, "hex");
        var payload = decodeProtoBuf_1.default(rawPayload);
        var metadata = {
            server_time: new Date().toISOString(),
            longitude: Number(messageKPN.LrrLON),
            latitude: Number(messageKPN.LrrLAT)
        };
        // convert payload
        var messageIot = {
            payload: payload,
            port: messageKPN.FPort,
            counter: messageKPN.FCntUp,
            dev_eui: messageKPN.DevEUI,
            metadata: metadata
        };
        // publish to exchange
        var messageAmqp = new amqp.Message(messageIot);
        this.destExchange.send(messageAmqp);
    };
    return ProcessKPN;
}());
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = ProcessKPN;

//# sourceMappingURL=processKPN.js.map
