/**
 * tests for processNotificationSlack
 *
 * 2016-10-18 Ab Reitsma
 */

require("./_logSettings");
import * as amqp from "amqp-ts";
import * as Slack from "node-slack";
import * as Chai from "chai";
var expect = Chai.expect;

import * as iot from "../code/iotMsg";
import ProcessNotificationSlack from "../code/processNotificationSlack";

import * as amqpSupport from "./_amqpIoTestSupport";

var amqpBrokerUrl = "amqp://rabbitmq";

// initialize support
amqpSupport.SetConnectionUrl({
  amqp: amqpBrokerUrl
});

// real slack connection
// var hook_url = "https://hooks.slack.com/services/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";
// var slack = new Slack(hook_url);
// dummy slack connection, always returns the expected results
var slack = <Slack>{
  send: (message: any ) => {
    return;
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
    new ProcessNotificationSlack(receiver, sender, slack);

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
  kanaal: 'slack',
  p1: 'koffer_1',
  p2: null,
  p3: null,
  p4: null,
  meldingtekst: 'TEST: Temperatuur te hoog: 65.3 C'
};

const notificationExpectedResult = {
      text: 'TEST: Temperatuur te hoog: 65.3 C',
      channel: 'koffer_1',
      username: "Sensormelding"
};
