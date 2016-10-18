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
var processObservation_1 = require("../code/processObservation");
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
        process.nextTick(callback, 0, [{
                id: 4,
                alarm_trigger: "true",
                omrekenfactor: "x ? \"dicht\" : \"open\""
            }]);
    }
};
describe("Test ProcessObservation", function () {
    it("should process alert rules for the observation", function (done) {
        var t = new amqpSupport.AmqpIoTest(done, true);
        var sender = new iot.SendMessagesAmqp(t.outExchange, false);
        var receiver = new iot.ReceiveMessagesAmqp(t.inQueue, false);
        // start the logging process
        new processObservation_1.default(receiver, sender, mysqlConnection);
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
    nodeId: '000000007FEE6E5B',
    sensorId: 1,
    sensorValue: "open",
    sensorValueType: 'stand',
    sensorError: 0,
    timestamp: '2016-10-03T13:30:10.829Z',
    logId: 9798
};
var observationExpectedResult = {
    nodeId: '000000007FEE6E5B',
    sensorId: 1,
    sensorValue: 'open',
    observationId: 9798,
    ruleId: 4
};

//# sourceMappingURL=processObservation.spec.js.map
