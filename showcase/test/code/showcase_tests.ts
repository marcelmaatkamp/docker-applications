/*
 * SHOWCASE-TESTS
 *
 * 2016-09-29 Ab Reitsma
 *
 * Programma dat test of de invoer via RabbitMQ voor ShowCase
 * overeenkomt met de verwachte uitvoer
 *
 */

import * as fs from "fs";
import * as path from "path";
import * as amqp from "amqp-ts";

import * as Promise from "bluebird";
//import * as deepEqual from "deep-equal";
import match from "./match";

const testTimeout = process.env.TEST_TIMEOUT || 1000; // timeout per test in ms

interface ExpectedMessage {
  result: Object | string;
  received?: boolean;
}

interface ExchangeResults {
  exchange: string;
  exchangeType: string;
  expectedMessages: ExpectedMessage[];
}

interface Test {
  description: string;
  sendMessage: string;
  sendExchange: string;
  sendExchangeType: string;
  expectedResults: ExchangeResults[];
  testTimeout?: number;
}

var testSet: Test[] = [];

class ShowcaseTest {
  private static testQueuePrefix = "showcase.test_queue_";
  private static testQueue = 0;
  private connection: amqp.Connection;
  private exchanges: {[key: string]: amqp.Exchange} = {};
  private consumers: amqp.Queue[] = [];
  private test: Test;
  private success = true;
  private completed = false;

  constructor(test: Test, connection?: amqp.Connection) {
    this.test = test;
    this.connection = connection || new amqp.Connection("amqp://rabbitmq");
  }

  private startExchangeConsumer(exchange: amqp.Exchange, exchangeResults: ExchangeResults) {
    ShowcaseTest.testQueue += 1;
    var queue = this.connection.declareQueue(ShowcaseTest.testQueuePrefix + ShowcaseTest.testQueue, {durable: false});
    queue.bind(exchange);
    var result = queue.startConsumer((msg) => {
      this.checkMessage(msg, exchangeResults);
    });
    this.consumers.push(queue);
  }

  private cleanupConsumers(): Promise<any> {
    // cleanup current consumers
    var await = [];
    for (let i = 0, len = this.consumers.length; i < len; i++) {
      await.push(this.consumers[i].stopConsumer().then(() => {
        return this.consumers[i].delete();
      }));
    }
    return Promise.all(await);
  }

  private checkMessage(msg, exchangeResults: ExchangeResults) {
    if (this.completed) { return; } // ignore messages sent after test finish
    var found = false;
    var expectedMessages = exchangeResults.expectedMessages;
    for (let i = 0, len = expectedMessages.length; i < len; i++) {
      //if (deepEqual(expectedMessages[i].result, msg)) {
      if (match(msg, expectedMessages[i].result)) {
        found = true;
        if (!expectedMessages[i].received) {
          expectedMessages[i].received = true;
          return;
        }
      }
    }
    this.success = false;
    if (found) {
      // todo: log message received too many times
      console.log("Message received too many times: " + JSON.stringify(msg));
    } else {
      // todo: log unexpected message received
      console.log("Unexpected message received: " + JSON.stringify(msg));
    }
  }

  // prepare test
  private prepareTest() {
    // create/connect to all exchanges

    this.exchanges[this.test.sendExchange] = this.connection.declareExchange(this.test.sendExchange, this.test.sendExchangeType);

    var results = this.test.expectedResults;
    var exchange: amqp.Exchange;
    for (let i = 0, len = results.length; i < len; i++) {
      if (!this.exchanges[results[i].exchange]) {
        exchange = this.connection.declareExchange(results[i].exchange, results[i].exchangeType);
        this.exchanges[results[i].exchange] = exchange;
        this.startExchangeConsumer(exchange, results[i]);
      }

      // exchange.activateConsumer((msg) => {
      //   this.checkMessage(msg, results[i]);
      // });
    }

    return this.connection.completeConfiguration();
  }

  private startTest() {
    var message = new amqp.Message(this.test.sendMessage);
    this.exchanges[this.test.sendExchange].send(message);
  }

  private checkResults() {
    // we are finished
    this.completed = true;
    // check if all expected messages have been received
    var results = this.test.expectedResults;
    for (let i = 0, len = results.length; i < len; i++) {
      let messages = results[i].expectedMessages;
      for (let j = 0, len = messages.length; j < len; j++) {
        if (!messages[j].received) {
          this.success = false;
          // todo: log expected result not received
          console.log("Expected result not received: " + JSON.stringify(messages[j].result));
        }
      }
    }
    return this.success;
  }

  private finishTest(): Promise<boolean> {
    return new Promise<boolean>((resolve, reject) => {
      setTimeout(() => {
        this.cleanupConsumers()
        .then(() => {
          resolve(this.checkResults());
        });
      }, this.test.testTimeout || testTimeout);
    });
  }

  public runTest(): Promise<boolean> {
    this.prepareTest()
    .then(() => {
      this.startTest();
    });
    return this.finishTest();
  }
}


var testfileLocation = path.join(__dirname, "..", "data", "tests.json");
var amqpConnection = new amqp.Connection("amqp://rabbitmq");
var testsBuffer = fs.readFileSync(testfileLocation).toString();
var tests = JSON.parse(testsBuffer);
var errorCount = 0;

var i = 0;

function executeTest() {
  "use strict";

  var currentTest = <Test>tests[i];

  var test = new ShowcaseTest(currentTest, amqpConnection);
  test.runTest()
  .then((success) => {
    if (!success) {
      errorCount += 1;
    }
    i += 1;
    if (i < tests.length) {
      executeTest();
    } else {
      // todo: display summary
      if (errorCount > 0) {
        console.log(errorCount + " of " + i + " tests generated errors.");
      } else {
        console.log(i + " tests completed without errors");
      }
      amqpConnection.close();
    }
  });
}
executeTest();
