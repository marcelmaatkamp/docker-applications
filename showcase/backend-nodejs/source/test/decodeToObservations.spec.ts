/**
 * tests for ProtocolBuffer
 *
 * 2016-10-11 Ab Reitsma
 */

import * as amqp from "amqp-ts";
import * as Chai from "chai";
import * as mysql from "mysql";
var expect = Chai.expect;

import * as iot from "../code/iotMsg";
import DecodeToObservations from "../code/decodeToObservations";

import * as amqpSupport from "./_amqpIoTestSupport";

var amqpBrokerUrl = "amqp://rabbitmq";

// initialize support
amqpSupport.SetConnectionUrl({
  amqp: amqpBrokerUrl
});

// real mysql database connection
// var mysqlConnection = mysql.createConnection({
//   host: "mysql",
//   user: "root",
//   password: "my-secret-pw",
//   database: "showcase"
// });
// dummy mysql, always returns the expected results
var mysqlConnection = <mysql.IConnection>{
  query: (queryString: string, callback: (err, results) => void) => {
    process.nextTick(callback, 0, [{
      omrekenfactor: "x ? \"dicht\" : \"open\"",
      eenheid: "stand"
    }]);
  }
};

function deepEquals(a, b) {
  try {
    expect(a).to.deep.equal(b);
    return true;
  } catch (_) {
    return false;
  }
}

// incomplete quick tests to see if all expected messages have been received
// does not check double results or extra results after all expected messages have been received
describe("Test DecodeToObservations", () => {
  it("should decode a message to observations", (done) => {
    var t = new amqpSupport.AmqpIoTest(done, true);
    var sender = new iot.SendMessagesAmqp(t.outExchange, false);
    var receiver = new iot.ReceiveMessagesAmqp(t.inQueue, false);

    var received1 = false;
    var received2 = false;
    //tslint:disable-next-line:no-unused-variable
    var processDecode = new DecodeToObservations(receiver, sender, mysqlConnection);

    t.outQueue.activateConsumer((msg) => {
      try {
        var content = msg.getContent();
        received1 = received1 || deepEquals(content, decodeExpectedResult1);
        received2 = received2 || deepEquals(content, decodeExpectedResult2);

        if (received1 && received2) {
          t.finish();
        }
      } catch (err) {
        t.finish(err);
      }
    }, { noAck: true });

    // make sure everything is connected before sending the test message
    t.startAll()
      .then(() => {
        var msg = new amqp.Message(decodeTestMessage1);
        t.inQueue.send(msg);
      });
  });

  it("should check for skipped messages (no skip)", (done) => {
    var t = new amqpSupport.AmqpIoTest(done, true);
    var sender = new iot.SendMessagesAmqp(t.outExchange, false);
    var receiver = new iot.ReceiveMessagesAmqp(t.inQueue, false);

    var received1 = false;
    var received2 = false;
    var received3 = false;
    var received4 = false;
    //tslint:disable-next-line:no-unused-variable
    var processDecode = new DecodeToObservations(receiver, sender, mysqlConnection);

    t.outQueue.activateConsumer((msg) => {
      try {
        var content = msg.getContent();
        received1 = received1 || deepEquals(content, decodeExpectedResult1);
        received2 = received2 || deepEquals(content, decodeExpectedResult2);
        received3 = received3 || deepEquals(content, decodeExpectedResult3);
        received4 = received4 || deepEquals(content, decodeExpectedResult4);

        if (received1 && received2 && received3 && received4) {
          t.finish();
        }
      } catch (err) {
        t.finish(err);
      }
    }, { noAck: true });

    // make sure everything is connected before sending the test message
    t.startAll()
      .then(() => {
        var msg = new amqp.Message(decodeTestMessage1);
        t.inQueue.send(msg);
        msg = new amqp.Message(decodeTestMessage2);
        t.inQueue.send(msg);
      });
  });

  it("should check for skipped messages (skip)", (done) => {
    var t = new amqpSupport.AmqpIoTest(done, true);
    var sender = new iot.SendMessagesAmqp(t.outExchange, false);
    var receiver = new iot.ReceiveMessagesAmqp(t.inQueue, false);

    var received1 = false;
    var received2 = false;
    var received3 = false;
    var received4 = false;
    var received5 = false;
    //tslint:disable-next-line:no-unused-variable
    var processDecode = new DecodeToObservations(receiver, sender, mysqlConnection);

    t.outQueue.activateConsumer((msg) => {
      try {
        var content = msg.getContent();
        received1 = received1 || deepEquals(content, decodeExpectedResult1);
        received2 = received2 || deepEquals(content, decodeExpectedResult2);
        received3 = received3 || deepEquals(content, decodeExpectedResult5);
        received4 = received4 || deepEquals(content, decodeExpectedResult6);
        received5 = received4 || deepEquals(content, decodeExpectedResult7);
        // console.log("received1: " + received1 + ", received2: " + received2);
        // console.log("received3: " + received3 + ", received4: " + received4);
        // console.log("received5: " + received5);
        // console.log("---------------------");
        // console.log(content);

        if (received1 && received2 && received3 && received4 && received5) {
          t.finish();
        }
      } catch (err) {
        t.finish(err);
      }
    }, { noAck: true });

    // make sure everything is connected before sending the test message
    t.startAll()
      .then(() => {
        var msg = new amqp.Message(decodeTestMessage1);
        t.inQueue.send(msg);
        msg = new amqp.Message(decodeTestMessage3);
        t.inQueue.send(msg);
      });
  });
});

/**
 * KPN test message and expected result
 */

const decodeTestMessage1 = {
  payload:
  [{
    id: 1,
    error: 0,
    value1: 1,
    value2: 0,
    value3: 0,
    value4: 0,
    value5: 0,
    value6: 0,
    value7: 0,
    value8: 0,
    value9: 0,
    value10: 0
  }],
  port: 1,
  counter: 1,
  dev_eui: "0059AC000018041B",
  metadata:
  [{
    server_time: "2016-10-03T13:30:10.829Z",
    longitude: 5.304723,
    latitude: 52.085842
  }]
};

const decodeTestMessage2 = {
  payload:
  [{
    id: 1,
    error: 0,
    value1: 1,
    value2: 0,
    value3: 0,
    value4: 0,
    value5: 0,
    value6: 0,
    value7: 0,
    value8: 0,
    value9: 0,
    value10: 0
  }],
  port: 1,
  counter: 2,
  dev_eui: "0059AC000018041B",
  metadata:
  [{
    server_time: "2016-10-03T13:31:10.829Z",
    longitude: 5.304723,
    latitude: 52.085842
  }]
};

const decodeTestMessage3 = {
  payload:
  [{
    id: 1,
    error: 0,
    value1: 1,
    value2: 0,
    value3: 0,
    value4: 0,
    value5: 0,
    value6: 0,
    value7: 0,
    value8: 0,
    value9: 0,
    value10: 0
  }],
  port: 1,
  counter: 3,
  dev_eui: "0059AC000018041B",
  metadata:
  [{
    server_time: "2016-10-03T13:32:10.829Z",
    longitude: 5.304723,
    latitude: 52.085842
  }]
};

const decodeExpectedResult1 = {
  nodeId: "0059AC000018041B",
  sensorId: 0,
  sensorValue: 1,
  sensorValueType: "alive",
  sensorError: 0,
  timestamp: "2016-10-03T13:30:10.829Z"
};

const decodeExpectedResult2 = {
  nodeId: "0059AC000018041B",
  sensorId: 1,
  sensorValue: "dicht",
  sensorError: 0,
  timestamp: "2016-10-03T13:30:10.829Z",
  sensorValueType: "stand"
};

const decodeExpectedResult3 = {
  nodeId: "0059AC000018041B",
  sensorId: 0,
  sensorValue: 1,
  sensorValueType: "alive",
  sensorError: 0,
  timestamp: "2016-10-03T13:31:10.829Z"
};

const decodeExpectedResult4 = {
  nodeId: "0059AC000018041B",
  sensorId: 1,
  sensorValue: "dicht",
  sensorError: 0,
  timestamp: "2016-10-03T13:31:10.829Z",
  sensorValueType: "stand"
};

const decodeExpectedResult5 = {
  nodeId: "0059AC000018041B",
  sensorId: 0,
  sensorValue: 1,
  sensorValueType: "alive",
  sensorError: 0,
  timestamp: "2016-10-03T13:32:10.829Z"
};

const decodeExpectedResult6 = {
  nodeId: "0059AC000018041B",
  sensorId: 1,
  sensorValue: "dicht",
  sensorError: 0,
  timestamp: "2016-10-03T13:32:10.829Z",
  sensorValueType: "stand"
};

const decodeExpectedResult7 = {
  nodeId: "0059AC000018041B",
  sensorId: -1,
  sensorValue: 1,
  sensorValueType: "skipped",
  sensorError: 0,
  timestamp: "2016-10-03T13:32:10.829Z"
};

