/*
 * SHOWCASE-TESTS
 *
 * 2016-09-29 Ab Reitsma
 *
 * Programma dat test of de invoer via RabbitMQ voor ShowCase
 * overeenkomt met de verwachte uitvoer
 *
 */
import * as Promise from "bluebird";
import * as fs from "fs";
import * as amqp from "./amqp-ts.d.ts";

const testTimeout = process.env.TEST_TIMEOUT || 1000; // timeout per test in ms

interface ExpectedMessage {
  result: string;
  received?: boolean;
}

interface ExchangeResults {
  exchange: string;
  expectedMessages: ExpectedMessage[];
}

interface Test {
  description: string;
  sendMessage: string;
  sendExchange: string;
  expectedResults: ExchangeResults[];
  testTimeout?: number;
}

var testSet: Test[] = [];

class ShowcaseTest {
  private connection: amqp.Connection;
  private exchanges: {[key: string]: amqp.Exchange};
  private test: Test;
  private success = true;

  constructor(test: Test, connection?: amqp.Connection) {
    this.test = test;
    this.connection = connection || new amqp.Connection("amqp://rabbitmq");
  }

  private checkMessage(msg: amqp.Message, exchangeResults: ExchangeResults) {
    var found = false;

    var expectedMessages = exchangeResults.expectedMessages;
    for (let i = 0, len = expectedMessages.length; i < len; i++) {
      if (msg.getContent() === expectedMessages[i].result) {
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
    } else {
      // todo: log unexpected message received
    }
  }

  // prepare test
  private prepareTest() {
    // create/connect to all exchanges
    this.exchanges[this.test.sendExchange] = this.connection.declareExchange(this.test.sendExchange);

    var results = this.test.expectedResults;
    var exchange: amqp.Exchange;
    for (let i = 0, len = results.length; i < len; i++) {
      if (!this.exchanges[results[i].exchange]) {
        exchange = this.connection.declareExchange(results[i].exchange);
        this.exchanges[results[i].exchange] = exchange;
      }

      exchange.activateConsumer((msg) => {
        this.checkMessage(msg, results[i]);
      });
    }

    return this.connection.completeConfiguration();
  }

  private startTest() {
    var message = new amqp.Message(this.test.sendMessage);
    this.exchanges[this.test.sendExchange].send(message);
  }

  private checkResults() {
    // check if all expected messages have been received
    var results = this.test.expectedResults;
    for (let i = 0, len = results.length; i < len; i++) {
      let messages = results[i].expectedMessages;
      for (let j = 0, len = messages.length; j < len; j++) {
        if (!messages[j].received) {
          this.success = false;
          // todo: log expected result not received
        }
      }
    }
  }

  private finishTest(): Promise<boolean> {
    return new Promise<boolean>((resolve, reject) => {
      setTimeout(() => {
        this.checkResults();
        resolve(this.success);
      }, this.test.testTimeout || testTimeout);
    });
  }

  public runTest(): Promise<boolean> {
    return this.prepareTest()
    .then(this.startTest)
    .then(this.finishTest);
  }
}



var testsBuffer = fs.readFileSync("../data/tests.json").toString();
var tests = JSON.parse(testsBuffer);
var errorCount = 0;

var i = 0;

function executeTest() {
  "use strict";

  var currentTest = <Test>tests[i];
  var test = new ShowcaseTest(currentTest);
  test.runTest()
  .then((errorsOccurred) => {
    if (errorsOccurred) {
      errorCount += 1;
    }
    i += 1;
    if (i < tests.length) {
      executeTest();
    } else {
      // todo: display summary
    }
  });
}
executeTest();
