/**
 * _AmqpIoTest.ts
 *
 * omschrijving
 *
 */

import * as amqp from "amqp-ts";
import * as Promise from "bluebird";

var amqpBrokerUrl: string;

export function SetConnectionUrl(urls: { amqp: string }) {
  amqpBrokerUrl = urls.amqp;
}

export class TestNumber {
  // create a unique number for each test to prevent tests from influencing each other
  private static _testNr = 1;
  static getTestNr(): number {
    return TestNumber._testNr++;
  }
}


// self containing test and cleanup support class
export class AmqpIoTest {
  amqpConnection: amqp.Connection;
  inQueue: amqp.Queue;
  outExchange: amqp.Exchange;
  outQueue: amqp.Queue;

  private done: (err?: any) => {};

  // create a unique number for each test to prevent tests from influencing each other
  testNr: number;

  constructor(done, createQueue?: boolean) {
    this.testNr = TestNumber.getTestNr();
    this.done = done;
    this.amqpConnection = new amqp.Connection(amqpBrokerUrl);
    this.inQueue = this.amqpConnection.declareQueue("test_" + this.testNr + ".showcase.inqueue" , { durable: false });
    this.outExchange = this.amqpConnection.declareExchange("test_" + this.testNr + ".showcase.outexchange" + this.testNr, "fanout", { durable: false });

    if (createQueue) {
      this.outQueue = this.amqpConnection.declareQueue("test_" + this.testNr + ".showcase.outqueue", { durable: false });
      this.outQueue.bind(this.outExchange);
    }
  }

  finish(err?: any) {
    this.amqpConnection.completeConfiguration()
      .then(() => {
        return this.amqpConnection.deleteConfiguration();
      })
      .then(() => {
        return this.amqpConnection.close();
      })
      .then(() => {
        this.done(err);
      })
      .catch((err) => {
        this.done(err);
      });
  }

  startAll(): Promise<void> {
    return this.amqpConnection.completeConfiguration();
  }
}
