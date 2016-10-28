/**
 * tests for processNotificationTelegram
 *
 * 2016-10-18 Ab Reitsma
 */
"use strict";
require("./_logSettings");
var amqp = require("amqp-ts");
// import * as Promise from "bluebird";
var Chai = require("chai");
var expect = Chai.expect;
var iot = require("../code/iotMsg");
var processNotificationSms_1 = require("../code/processNotificationSms");
var amqpSupport = require("./_amqpIoTestSupport");
// unfortunately no typescript .d.ts exists for node-telegram-bot-api
var amqpBrokerUrl = "amqp://rabbitmq";
// initialize support
amqpSupport.SetConnectionUrl({
    amqp: amqpBrokerUrl
});
// twilio source phone number
var twilioFromPhone = "+19787124065";
// create twilio connection
// var twilioAccountSid = "AC600a293801150c7c3af3a5747a3ba4ae";
// var twilioAuthToken = "ad1f82c56f5b9f048e72558ae984edf8";
// var twilioClient = require("twilio")(twilioAccountSid, twilioAuthToken);
// dummy twilio connection, always returns the expected results
var twilioClient = {
    sendMessage: function (msgStruct, callback) {
        callback(null, {
            to: msgStruct.to,
            from: twilioFromPhone,
            body: msgStruct.body
        });
    }
};
// incomplete quick test to see if no errors occur,
// actual testing to see if the sms notification works is not performed
describe("Test ProcessNotificationSms", function () {
    it("should process the sms notification", function (done) {
        var t = new amqpSupport.AmqpIoTest(done, true);
        var sender = new iot.SendMessagesAmqp(t.outExchange, false);
        var receiver = new iot.ReceiveMessagesAmqp(t.inQueue, false);
        // start the logging process
        new processNotificationSms_1.default(receiver, sender, twilioClient, twilioFromPhone);
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
 * sms notification test message and expected results
 */
var alertTestMessage = {
    kanaal: "sms",
    p1: "+31640053814",
    p2: null,
    p3: null,
    p4: null,
    meldingtekst: "TEST: Temperatuur te hoog: 65.3 C"
};
var notificationExpectedResult = {
    to: "+31640053814",
    from: "+19787124065",
    body: "TEST: Temperatuur te hoog: 65.3 C",
};

//# sourceMappingURL=processNotificationSms.spec.js.map
