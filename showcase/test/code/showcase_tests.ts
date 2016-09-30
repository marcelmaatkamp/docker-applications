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

const testTimeout = 1000; // timeout per test in ms

// TODO: test framework ontwerpen en bouwen

// ontwerp
// volgorde:
// 1. lees json bestand met input en verwachte output
// json bestandsformaat:

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
  private missedResults: {[key: string]: boolean};

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
    if (found) {
      // message received too many times
    } else {
      // unexpected message received
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

  private finishTest() {
    // wait until timeout has exceeded, then check if all messages have been received
    // notify missing messages
    // return promise containing test results that resolves when tests are completed
  }
}

// initialize tests
// TODO




// execute test

// cleanup test

// log test


