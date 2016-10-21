/**
 * tests for processNotificationSlack
 *
 * 2016-10-18 Ab Reitsma
 */
"use strict";
var amqp = require("amqp-ts");
var Promise = require("bluebird");
var Chai = require("chai");
var expect = Chai.expect;
var iot = require("../code/iotMsg");
var processNotificationTelegram_1 = require("../code/processNotificationTelegram");
var amqpSupport = require("./_amqpIoTestSupport");
// unfortunately no typescript .d.ts exists for node-telegram-bot-api
var amqpBrokerUrl = "amqp://rabbitmq";
// initialize support
amqpSupport.SetConnectionUrl({
    amqp: amqpBrokerUrl
});
// create telegram connection
// var TelegramBot = require("node-telegram-bot-api");
// var telegramBotToken = "292441232:AAHS3zE8dyJWRUCx29bLx-MOwWEpimRt0mk";
// var telegramBot = new TelegramBot(telegramBotToken);
// dummy telegram connection, always returns the expected results
var telegramBot = {
    sendMessage: function (chatId, message) {
        return Promise.resolve();
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
        new processNotificationTelegram_1.default(receiver, sender, telegramBot);
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
    kanaal: 'telegram',
    p1: '-1001097331998',
    p2: null,
    p3: null,
    p4: null,
    meldingtekst: 'TEST: Temperatuur te hoog: 65.3 C'
};
var notificationExpectedResult = {
    text: 'TEST: Temperatuur te hoog: 65.3 C',
    chatId: '-1001097331998'
};

//# sourceMappingURL=processNotificationTelegram.spec.js.map
