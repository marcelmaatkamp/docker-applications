/**
 * tests for ProcessAlert
 *
 * 2016-10-18 Ab Reitsma
 */

require("./_logSettings");
import * as amqp from "amqp-ts";
import * as Chai from "chai";
import * as mysql from "mysql";
var expect = Chai.expect;

import * as iot from "../code/iotMsg";
import ProcessAlert from "../code/processAlert";

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
      kanaal: 'slack',
      p1: 'koffer_1',
      p2: null,
      p3: null,
      p4: null,
      meldingtekst: 'TEST: Temperatuur te hoog: %v %t'
    }, {
      kanaal: 'telegram',
      p1: null,
      p2: null,
      p3: null,
      p4: null,
      meldingtekst: 'TEST: Temperatuur te hoog: %v %t'
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
describe("Test ProcessAlert", () => {
  it("should process alert notification distribution", (done) => {
    var t = new amqpSupport.AmqpIoTest(done, true);
    var sender = new iot.SendMessagesAmqp(t.outExchange, false);
    var receiver = new iot.ReceiveMessagesAmqp(t.inQueue, false);

    var received1 = false;
    var received2 = false;
    // start the logging process
    new ProcessAlert(receiver, sender, mysqlConnection);

    t.outQueue.activateConsumer((msg) => {
      try {
        var content = msg.getContent();
        received1 = received1 || deepEquals(content, alertExpectedResult1);
        received2 = received2 || deepEquals(content, alertExpectedResult2);

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
        var msg = new amqp.Message(alertTestMessage);
        t.inQueue.send(msg);
      });
  });
});


/**
 * alert test message and expected results
 */
const alertTestMessage = {
  nodeId: '000000007FEE6E5B',
  sensorId: 2,
  sensorValue: 65.3,
  observationId: 9798,
  ruleId: 5,
  sensorValueType: "C"
};

const alertExpectedResult1 = {
  kanaal: 'slack',
  p1: 'koffer_1',
  p2: null,
  p3: null,
  p4: null,
  meldingtekst: 'TEST: Temperatuur te hoog: 65.3 C'
};

const alertExpectedResult2 = {
  kanaal: 'telegram',
  p1: null,
  p2: null,
  p3: null,
  p4: null,
  meldingtekst: 'TEST: Temperatuur te hoog: 65.3 C'
};
