/**
 * tests for processNotificationTelegram
 *
 * 2016-10-18 Ab Reitsma
 */

require("./_logSettings");
import * as amqp from "amqp-ts";
import * as Promise from "bluebird";
import * as Chai from "chai";
var expect = Chai.expect;

import * as iot from "../code/iotMsg";
import ProcessNotificationTelegram from "../code/processNotificationTelegram";

import * as amqpSupport from "./_amqpIoTestSupport";

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
  sendMessage: (chatId, message) => {
    return Promise.resolve();
  }
};


// incomplete quick test to see if no errors occur,
// actual testing to see if the slack notification works is not performed
describe("Test ProcessNotificationSlack", () => {
  it("should process the slack notification", (done) => {
    var t = new amqpSupport.AmqpIoTest(done, true);
    var sender = new iot.SendMessagesAmqp(t.outExchange, false);
    var receiver = new iot.ReceiveMessagesAmqp(t.inQueue, false);

    // start the logging process
    new ProcessNotificationTelegram(receiver, sender, telegramBot);

    t.outQueue.activateConsumer((msg) => {
      try {
        var content = msg.getContent();
        expect(content).to.deep.equal(notificationExpectedResult);
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
 * slack notification test message and expected results
 */
const alertTestMessage = {
  kanaal: 'telegram',
  p1: '-1001097331998',
  p2: null,
  p3: null,
  p4: null,
  meldingtekst: 'TEST: Temperatuur te hoog: 65.3 C'
};

const notificationExpectedResult = {
      text: 'TEST: Temperatuur te hoog: 65.3 C',
      chatId: '-1001097331998'
};
