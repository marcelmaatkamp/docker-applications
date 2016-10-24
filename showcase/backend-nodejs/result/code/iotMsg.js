/**
 * iotMessage type definitions and message send and receive classes
 *
 * 2016-10-11 Ab Reitsma
 */
"use strict";
var winston = require("winston");
var amqp = require("amqp-ts");
var Promise = require("bluebird");
/**
 * Generic abstract class to decouple the sending of a message from a particular implementation
 */
var SendMessages = (function () {
    function SendMessages() {
    }
    SendMessages.prototype.send = function (msg) { };
    ;
    return SendMessages;
}());
exports.SendMessages = SendMessages;
/**
 * Implementation of SendMessage with AMQP
 */
var SendMessagesAmqp = (function () {
    /**
     * Create a SendMessage instance for amqp
     * @param (amqp.Exchange) amqpExchange - the amqp Exchange
     * @param (boolean) [inNodeRedEnvelope=false] - send the message in a NedeRed envelope
     */
    function SendMessagesAmqp(amqpExchange, inNodeRedEnvelope) {
        if (inNodeRedEnvelope === void 0) { inNodeRedEnvelope = true; }
        this.amqpExchange = amqpExchange;
        this.inNodeRedEnvelope = inNodeRedEnvelope;
    }
    SendMessagesAmqp.prototype.send = function (msg) {
        if (this.inNodeRedEnvelope) {
            var msg = {
                payload: msg
            };
        }
        var amqpMsg = new amqp.Message(msg);
        this.amqpExchange.send(amqpMsg);
        winston.debug("Message sent to AMQP exchange '" + this.amqpExchange.name + "'", amqpMsg);
    };
    return SendMessagesAmqp;
}());
exports.SendMessagesAmqp = SendMessagesAmqp;
/**
 * Generic abstract class to decouple the message receiving from a particular implementation
 */
var ReceiveMessages = (function () {
    function ReceiveMessages() {
    }
    ReceiveMessages.prototype.startConsumer = function (msgReceiver) { };
    ReceiveMessages.prototype.stopConsumer = function () { return Promise.resolve(); };
    return ReceiveMessages;
}());
exports.ReceiveMessages = ReceiveMessages;
var ReceiveMessagesAmqp = (function () {
    /**
     * Create a ReceiveMessage instance for amqp
     * @param (amqp.Queue) amqpQueue - the amqp Exchange
     * @param (boolean) [inNodeRedEnvelope=false] - send the message in a NedeRed envelope
     */
    function ReceiveMessagesAmqp(amqpQueue, inNodeRedEnvelope) {
        if (inNodeRedEnvelope === void 0) { inNodeRedEnvelope = true; }
        this.amqpQueue = amqpQueue;
        this.inNodeRedEnvelope = inNodeRedEnvelope;
    }
    ReceiveMessagesAmqp.prototype.receiveMessage = function (msg) {
        var content = msg.content.toString();
        try {
            content = JSON.parse(content);
        }
        catch (_) { }
        if (this.inNodeRedEnvelope) {
            content = content.payload;
        }
        winston.debug("Message received from AMQP exchange '" + this.amqpQueue.name + "'", content);
        this.msgReceiver(content);
    };
    ReceiveMessagesAmqp.prototype.startConsumer = function (msgReceiver) {
        var _this = this;
        if (this.msgReceiver) {
            //todo: log error
            throw new Error("ReceiveAmqpMessages.startReceiving: receiver already started!");
        }
        this.msgReceiver = msgReceiver;
        this.amqpQueue.activateConsumer(function (msg) {
            _this.receiveMessage(msg);
        }, { noAck: true });
    };
    ReceiveMessagesAmqp.prototype.stopConsumer = function () {
        var _this = this;
        return this.amqpQueue.stopConsumer()
            .then(function () {
            delete _this.msgReceiver;
        });
    };
    return ReceiveMessagesAmqp;
}());
exports.ReceiveMessagesAmqp = ReceiveMessagesAmqp;
var AmqpInOut = (function () {
    function AmqpInOut(create) {
        this.outExchange = this.getExchange(create.out);
        this.send = new SendMessagesAmqp(this.outExchange);
        var inExchange = this.getExchange(create.in);
        if (inExchange) {
            this.inQueue = AmqpInOut.amqpConnection.declareQueue(inExchange.name + "." + AmqpInOut.amqpQueueSuffix + this.nextQueueNr(), { durable: false });
            this.inQueue.bind(inExchange);
            this.receive = new ReceiveMessagesAmqp(this.inQueue);
        }
    }
    AmqpInOut.preInitialize = function (amqpConnection, amqpQueueSuffix) {
        AmqpInOut.amqpConnection = amqpConnection;
        AmqpInOut.amqpQueueSuffix = amqpQueueSuffix;
    };
    AmqpInOut.prototype.nextQueueNr = function () {
        return AmqpInOut._queueNr++;
    };
    AmqpInOut.prototype.getExchange = function (exchange) {
        if (typeof exchange === "string") {
            return AmqpInOut.amqpConnection.declareExchange(exchange, "fanout");
        }
        // expect it to be an amqp.Exchange or null
        return exchange;
    };
    AmqpInOut._queueNr = 1;
    return AmqpInOut;
}());
exports.AmqpInOut = AmqpInOut;

//# sourceMappingURL=iotMsg.js.map
