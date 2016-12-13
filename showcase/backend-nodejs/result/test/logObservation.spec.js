/**
 * tests for logObservation
 *
 * 2016-10-11 Ab Reitsma
 */
"use strict";
require("./_logSettings");
var amqp = require("amqp-ts");
var Chai = require("chai");
var expect = Chai.expect;
var iot = require("../code/iotMsg");
var logObservation_1 = require("../code/logObservation");
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
            insertId: 9798,
            serverStatus: 2,
            warningCount: 0,
            message: "",
            protocol41: true,
            changedRows: 0
        });
    }
};
describe("Test LogObservation", function () {
    it("should log an observations in the database", function (done) {
        var t = new amqpSupport.AmqpIoTest(done, true);
        var sender = new iot.SendMessagesAmqp(t.outExchange, false);
        var receiver = new iot.ReceiveMessagesAmqp(t.inQueue, false);
        // start the logging process
        new logObservation_1.default(receiver, sender, mysqlConnection);
        t.outQueue.activateConsumer(function (msg) {
            try {
                var content = msg.getContent();
                expect(content).to.deep.equal(observationExpectedResult);
                t.finish();
            }
            catch (err) {
                t.finish(err);
            }
        }, { noAck: true });
        // make sure everything is connected before sending the test message
        t.startAll()
            .then(function () {
            var msg = new amqp.Message(observationTestMessage);
            t.inQueue.send(msg);
        });
    });
});
/**
 * observation test message and expected result
 */
var observationTestMessage = {
    nodeId: "0059AC000018041B",
    sensorId: 0,
    sensorValue: 1,
    sensorValueType: "alive",
    sensorError: 0,
    timestamp: "2016-10-03T13:30:10.829Z"
};
var observationExpectedResult = {
    nodeId: '0059AC000018041B',
    sensorId: 0,
    sensorValue: 1,
    sensorValueType: 'alive',
    sensorError: 0,
    timestamp: '2016-10-03T13:30:10.829Z',
    logId: 9798
};

//# sourceMappingURL=logObservation.spec.js.map
