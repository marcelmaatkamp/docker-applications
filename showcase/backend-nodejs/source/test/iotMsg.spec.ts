/**
 * tests for ProtocolBuffer
 *
 * 2016-10-11 Ab Reitsma
 */

import * as amqp from "amqp-ts";
import * as Chai from "chai";
var expect = Chai.expect;

import * as iot from "../code/iotMsg";

import * as amqpSupport from "./_amqpIoTestSupport";

var amqpBrokerUrl = "amqp://rabbitmq";

// initialize support
amqpSupport.SetConnectionUrl({
  amqp: amqpBrokerUrl
});


describe("Test iotMsg SendMessageAmqp and ReceiveMessageAmqp", () => {
  it("should be able to send and receive an amqp message", (done) => {
    var t = new amqpSupport.AmqpIoTest(done, true);
    var sendMsg = new iot.SendMessagesAmqp(t.outExchange);
    var receiveMsg = new iot.ReceiveMessagesAmqp(t.outQueue);
    receiveMsg.startConsumer((msg) => {
      try {
        expect(msg).to.equal("test");
        t.finish();
      } catch (err) {
        t.finish(err);
      }
    });

    t.startAll()
      .then(() => {
        sendMsg.send("test");
      })
      .catch((err) => {
        t.finish(err);
      });
  });

  it("should be able to send an amqp message without NodeRed envelope", (done) => {
    var t = new amqpSupport.AmqpIoTest(done, true);
    var sendMsg = new iot.SendMessagesAmqp(t.outExchange, false);
    t.outQueue.activateConsumer((msg) => {
      var content = msg.content.toString();
      try {
        expect(content).to.equal("test");
        t.finish();
      } catch (err) {
        t.finish(err);
      }
    }, { noAck: true });

    t.startAll()
      .then(() => {
        sendMsg.send("test");
      })
      .catch((err) => {
        t.finish(err);
      });
  });

  it("should be able to send an amqp message with NodeRed envelope", (done) => {
    var t = new amqpSupport.AmqpIoTest(done, true);
    var sendMsg = new iot.SendMessagesAmqp(t.outExchange, true);
    t.outQueue.activateConsumer((msg) => {
      var content = msg.content.toString();
      try {
        expect(content).to.equal("{\"payload\":\"test\"}");
        t.finish();
      } catch (err) {
        t.finish(err);
      }
    }, { noAck: true });

    t.startAll()
      .then(() => {
        sendMsg.send("test");
      })
      .catch((err) => {
        t.finish(err);
      });
  });

  it("should be able to receive an amqp message without NodeRed envelope", (done) => {
    var t = new amqpSupport.AmqpIoTest(done, true);
    var receiveMsg = new iot.ReceiveMessagesAmqp(t.outQueue, false);
    receiveMsg.startConsumer((msg) => {
      try {
        expect(msg).to.equal("test");
        t.finish();
      } catch (err) {
        t.finish(err);
      }
    });

    t.startAll()
      .then(() => {
        var msg = new amqp.Message("test");
        t.outExchange.send(msg);
      })
      .catch((err) => {
        t.finish(err);
      });
  });

  it("should be able to receive an amqp message with NodeRed envelope", (done) => {
    var t = new amqpSupport.AmqpIoTest(done, true);
    var receiveMsg = new iot.ReceiveMessagesAmqp(t.outQueue, true);
    receiveMsg.startConsumer((msg) => {
      try {
        expect(msg).to.equal("test");
        t.finish();
      } catch (err) {
        t.finish(err);
      }
    });

    t.startAll()
      .then(() => {
        var msg = new amqp.Message({payload: "test"});
        t.outExchange.send(msg);
      })
      .catch((err) => {
        t.finish(err);
      });
  });
});

describe("Test iotMsg AmqpInOut", () => {
  it("should be able to create an amqp in queue and out exchange", (done) => {
//todo: write test
    done();
  });
});
