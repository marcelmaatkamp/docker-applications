/**
 * tests for ProcessAlert
 *
 * 2016-10-18 Ab Reitsma
 */

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
var mysqlConnection = mysql.createConnection({
  host: "mysql",
  user: "root",
  password: "my-secret-pw",
  database: "showcase"
});
// dummy mysql, always returns the expected results
// var mysqlConnection = <mysql.IConnection>{
//   query: (queryString: string, callback: (err, results) => void) => {
//     process.nextTick(callback, 0, [{
//       id: 3,
//       alarm_trigger: "false",
//       omrekenfactor: "x ? \"dicht\" : \"open\""
//     },
//     {
//       id: 4,
//       alarm_trigger: "true",
//       omrekenfactor: "x ? \"dicht\" : \"open\""
//     }]);
//   }
// };

describe("Test ProcessAlert", () => {
  it("should process alert notification distribution", (done) => {
    var t = new amqpSupport.AmqpIoTest(done, true);
    var sender = new iot.SendMessagesAmqp(t.outExchange, false);
    var receiver = new iot.ReceiveMessagesAmqp(t.inQueue, false);

    // start the logging process
    new ProcessAlert(receiver, sender, mysqlConnection);

    t.outQueue.activateConsumer((msg) => {
      try {
        var content = msg.getContent();
        expect(content).to.deep.equal(alertExpectedResult);
        t.finish();
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
 * observation test message and expected result
 */
const alertTestMessage = {
  nodeId: '000000007FEE6E5B',
  sensorId: 1,
  sensorValue: 'open',
  observationId: 9798,
  ruleId: 4,
  sensorValueType: "stand"
};

const alertExpectedResult = {};
