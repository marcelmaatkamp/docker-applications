/**
 * tests for logAlert
 *
 * 2016-10-11 Ab Reitsma
 */

import * as amqp from "amqp-ts";
import * as Chai from "chai";
import * as mysql from "mysql";
var expect = Chai.expect;

import * as iot from "../code/iotMsg";
import LogAlert from "../code/logAlert";

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
    process.nextTick(callback, 0, {
      fieldCount: 0,
      affectedRows: 1,
      insertId: 5,
      serverStatus: 2,
      warningCount: 0,
      message: '',
      protocol41: true,
      changedRows: 0
    });
  }
};

describe("Test LogAlert", () => {
  it("should log an alert in the database", (done) => {
    var t = new amqpSupport.AmqpIoTest(done, true);
    var sender = new iot.SendMessagesAmqp(t.outExchange, false);
    var receiver = new iot.ReceiveMessagesAmqp(t.inQueue, false);

    // start the logging process
    new LogAlert(receiver, sender, mysqlConnection);

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
 * alert test message and expected result
 */
const alertTestMessage = {
  nodeId: '000000007FEE6E5B',
  sensorId: 2,
  sensorValue: 65.3,
  observationId: 9798,
  ruleId: 5,
  sensorValueType: "C"
};

const alertExpectedResult = {
  nodeId: '000000007FEE6E5B',
  sensorId: 2,
  sensorValue: 65.3,
  observationId: 9798,
  ruleId: 5,
  sensorValueType: "C",
  logId: 5
};
