/**
 * tests for ProtocolBuffer
 *
 * 2016-10-11 Ab Reitsma
 */
"use strict";
var amqp = require("amqp-ts");
var Chai = require("chai");
var expect = Chai.expect;
var iot = require("../code/iotMsg");
var amqpSupport = require("./_amqpIoTestSupport");
var amqpBrokerUrl = "amqp://rabbitmq";
// initialize support
amqpSupport.SetConnectionUrl({
    amqp: amqpBrokerUrl
});
describe("Test iotMsg SendMessageAmqp and ReceiveMessageAmqp", function () {
    it("should be able to send and receive an amqp message", function (done) {
        var t = new amqpSupport.AmqpIoTest(done, true);
        var sendMsg = new iot.SendMessagesAmqp(t.outExchange);
        var receiveMsg = new iot.ReceiveMessagesAmqp(t.outQueue);
        receiveMsg.startConsumer(function (msg) {
            try {
                expect(msg).to.equal("test");
                t.finish();
            }
            catch (err) {
                t.finish(err);
            }
        });
        t.startAll()
            .then(function () {
            sendMsg.send("test");
        })
            .catch(function (err) {
            t.finish(err);
        });
    });
    it("should be able to send an amqp message without NodeRed envelope", function (done) {
        var t = new amqpSupport.AmqpIoTest(done, true);
        var sendMsg = new iot.SendMessagesAmqp(t.outExchange, false);
        t.outQueue.activateConsumer(function (msg) {
            var content = msg.content.toString();
            try {
                expect(content).to.equal("test");
                t.finish();
            }
            catch (err) {
                t.finish(err);
            }
        }, { noAck: true });
        t.startAll()
            .then(function () {
            sendMsg.send("test");
        })
            .catch(function (err) {
            t.finish(err);
        });
    });
    it("should be able to send an amqp message with NodeRed envelope", function (done) {
        var t = new amqpSupport.AmqpIoTest(done, true);
        var sendMsg = new iot.SendMessagesAmqp(t.outExchange, true);
        t.outQueue.activateConsumer(function (msg) {
            var content = msg.content.toString();
            try {
                expect(content).to.equal("{\"payload\":\"test\"}");
                t.finish();
            }
            catch (err) {
                t.finish(err);
            }
        }, { noAck: true });
        t.startAll()
            .then(function () {
            sendMsg.send("test");
        })
            .catch(function (err) {
            t.finish(err);
        });
    });
    it("should be able to receive an amqp message without NodeRed envelope", function (done) {
        var t = new amqpSupport.AmqpIoTest(done, true);
        var receiveMsg = new iot.ReceiveMessagesAmqp(t.outQueue, false);
        receiveMsg.startConsumer(function (msg) {
            try {
                expect(msg).to.equal("test");
                t.finish();
            }
            catch (err) {
                t.finish(err);
            }
        });
        t.startAll()
            .then(function () {
            var msg = new amqp.Message("test");
            t.outExchange.send(msg);
        })
            .catch(function (err) {
            t.finish(err);
        });
    });
    it("should be able to receive an amqp message with NodeRed envelope", function (done) {
        var t = new amqpSupport.AmqpIoTest(done, true);
        var receiveMsg = new iot.ReceiveMessagesAmqp(t.outQueue, true);
        receiveMsg.startConsumer(function (msg) {
            try {
                expect(msg).to.equal("test");
                t.finish();
            }
            catch (err) {
                t.finish(err);
            }
        });
        t.startAll()
            .then(function () {
            var msg = new amqp.Message({ payload: "test" });
            t.outExchange.send(msg);
        })
            .catch(function (err) {
            t.finish(err);
        });
    });
});
describe("Test iotMsg AmqpInOut", function () {
    it("should be able to create an amqp in queue and out exchange", function (done) {
        //todo: write test
        done();
    });
});

//# sourceMappingURL=iotMsg.spec.js.map
