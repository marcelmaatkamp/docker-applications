/**
 * tests for logAlert
 *
 * 2016-10-11 Ab Reitsma
 */
"use strict";
require("./_logSettings");
var amqp = require("amqp-ts");
var Chai = require("chai");
var expect = Chai.expect;
var iot = require("../code/iotMsg");
var logAlert_1 = require("../code/logAlert");
var amqpSupport = require("./_amqpIoTestSupport");
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
var mysqlConnection = {
    query: function (queryString, callback) {
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
describe("Test LogAlert", function () {
    it("should log an alert in the database", function (done) {
        var t = new amqpSupport.AmqpIoTest(done, true);
        var sender = new iot.SendMessagesAmqp(t.outExchange, false);
        var receiver = new iot.ReceiveMessagesAmqp(t.inQueue, false);
        // start the logging process
        new logAlert_1.default(receiver, sender, mysqlConnection);
        t.outQueue.activateConsumer(function (msg) {
            try {
                var content = msg.getContent();
                expect(content).to.deep.equal(alertExpectedResult);
                t.finish();
            }
            catch (err) {
                t.finish(err);
            }
        }, { noAck: true });
        // make sure everything is connected before sending the test message
        t.startAll()
            .then(function () {
            var msg = new amqp.Message(alertTestMessage);
            t.inQueue.send(msg);
        });
    });
});
/**
 * alert test message and expected result
 */
var alertTestMessage = {
    nodeId: '000000007FEE6E5B',
    sensorId: 2,
    sensorValue: 65.3,
    observationId: 9798,
    ruleId: 5,
    sensorValueType: "C"
};
var alertExpectedResult = {
    nodeId: '000000007FEE6E5B',
    sensorId: 2,
    sensorValue: 65.3,
    observationId: 9798,
    ruleId: 5,
    sensorValueType: "C",
    logId: 5
};

//# sourceMappingURL=logAlert.spec.js.map
