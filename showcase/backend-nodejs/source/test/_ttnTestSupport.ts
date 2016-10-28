/**
 * testToolsTTN.ts
 *
 * omschrijving
 *
 */

import * as mqtt from "mqtt";
import * as amqp from "amqp-ts";
import * as Promise from "bluebird";

import * as amqpSupport from "./_amqpIoTestSupport";

var mqttBrokerUrl: string;
var amqpBrokerUrl: string;

export function SetConnectionUrl(urls: { mqtt: string, amqp: string }) {
  mqttBrokerUrl = urls.mqtt;
  amqpBrokerUrl = urls.amqp;
}

// self containing test and cleanup support class
export class TtnToMessageTest {
  mqttClient: mqtt.Client;
  amqpConnection: amqp.Connection;
  exchange: amqp.Exchange;
  queue: amqp.Queue;

  private done: (err?: any) => {};
  private doneSent = false;
  private errorMessage: any;
  private mqttFinished = false;
  private amqpFinished = false;

  testNr: number;


  constructor(done, createQueue?: boolean) {
    this.testNr = amqpSupport.TestNumber.getTestNr();
    this.done = done;
    this.mqttClient = mqtt.connect(mqttBrokerUrl);
    this.amqpConnection = new amqp.Connection(amqpBrokerUrl);
    this.exchange = this.amqpConnection.declareExchange("test_" + this.testNr + ".showcase.ttnexchange", "fanout", { durable: false });
    this.mqttClient.on("error", (err) => {
      this.finish(err);
    });
    this.mqttClient.on("close", (err) => {
      this.mqttFinished = true;
      this.allFinished();
    });

    if (createQueue) {
      this.queue = this.amqpConnection.declareQueue("test_" + this.testNr + ".showcase.ttnqueue", { durable: false });
      this.queue.bind(this.exchange);
    }
  }

  allFinished() {
    if (this.mqttFinished && this.amqpFinished && !this.doneSent) {
      this.doneSent = true;
      this.done(this.errorMessage);
    }
  }

  finish(err?: any) {
    this.errorMessage = err;
    this.amqpConnection.completeConfiguration()
      .then(() => {
        this.amqpConnection.deleteConfiguration()
          .then(() => {
            this.amqpConnection.close();
          });
      })
      .then(() => {
        this.amqpFinished = true;
        this.allFinished();
      })
      .catch((err) => {
        this.errorMessage = err;
        this.allFinished();
      });

    this.mqttClient.end(true);
  }

  startAll(): Promise<void> {
    return new Promise<void>((resolve, reject) => {
      this.mqttClient.on("connect", () => {
        this.amqpConnection.completeConfiguration()
          .then(() => {
            resolve();
          })
          .catch((err) => {
            reject(err);
          });
      });
    });
  }
}
