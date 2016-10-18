/**
 * tests for ProcessAlert
 *
 * 2016-10-18 Ab Reitsma
 */
"use strict";
var amqp = require("amqp-ts");
var Chai = require("chai");
var mysql = require("mysql");
var expect = Chai.expect;
var iot = require("../code/iotMsg");
var processAlert_1 = require("../code/processAlert");
var amqpSupport = require("./_amqpIoTestSupport");
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
describe("Test ProcessAlert", function () {
    it("should process alert notification distribution", function (done) {
        var t = new amqpSupport.AmqpIoTest(done, true);
        var sender = new iot.SendMessagesAmqp(t.outExchange, false);
        var receiver = new iot.ReceiveMessagesAmqp(t.inQueue, false);
        // start the logging process
        new processAlert_1.default(receiver, sender, mysqlConnection);
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
 * observation test message and expected result
 */
var alertTestMessage = {
    nodeId: '000000007FEE6E5B',
    sensorId: 1,
    sensorValue: 'open',
    observationId: 9798,
    ruleId: 4,
    sensorValueType: "stand"
};
var alertExpectedResult = {};

//# sourceMappingURL=processAlert.spec.js.map
