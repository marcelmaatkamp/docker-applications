/**
 * tests for processNotificationSlack
 *
 * 2016-10-18 Ab Reitsma
 */
"use strict";
var amqp = require("amqp-ts");
var Chai = require("chai");
var expect = Chai.expect;
var iot = require("../code/iotMsg");
var processNotificationSlack_1 = require("../code/processNotificationSlack");
var amqpSupport = require("./_amqpIoTestSupport");
var amqpBrokerUrl = "amqp://rabbitmq";
// initialize support
amqpSupport.SetConnectionUrl({
    amqp: amqpBrokerUrl
});
// real slack connection
// var hook_url = "https://hooks.slack.com/services/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";
// var slack = new Slack(hook_url);
// dummy slack connection, always returns the expected results
var slack = {
    send: function (message) {
        return;
    }
};
// incomplete quick test to see if no errors occur,
// actual testing to see if the slack notification works is not performed
describe("Test ProcessNotificationSlack", function () {
    it("should process the slack notification", function (done) {
        var t = new amqpSupport.AmqpIoTest(done, true);
        var sender = new iot.SendMessagesAmqp(t.outExchange, false);
        var receiver = new iot.ReceiveMessagesAmqp(t.inQueue, false);
        // start the logging process
        new processNotificationSlack_1.default(receiver, sender, slack);
        t.outQueue.activateConsumer(function (msg) {
            try {
                var content = msg.getContent();
                expect(content).to.deep.equal(notificationExpectedResult);
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
 * slack notification test message and expected results
 */
var alertTestMessage = {
    kanaal: 'slack',
    p1: 'koffer_1',
    p2: null,
    p3: null,
    p4: null,
    meldingtekst: 'TEST: Temperatuur te hoog: 65.3 C'
};
var notificationExpectedResult = {
    text: 'TEST: Temperatuur te hoog: 65.3 C',
    channel: 'koffer_1',
    username: "Sensormelding"
};

//# sourceMappingURL=processNotificationSlack.spec.js.map
