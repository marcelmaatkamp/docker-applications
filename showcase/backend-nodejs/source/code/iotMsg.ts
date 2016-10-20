/**
 * iotMessage type definitions and message send and receive classes
 *
 * 2016-10-11 Ab Reitsma
 */

import * as amqp from "amqp-ts";
import * as Promise from "bluebird";



// protobuf format:
// syntax = "proto2";
//
// message SensorReading {
// 	optional uint32 id = 1;
// 	optional uint32 error = 2;
// 	optional sint32 value1 = 3;
// 	optional sint32 value2 = 4;
// 	optional sint32 value3 = 5;
// 	optional sint32 value4 = 6;
// 	optional sint32 value5 = 7;
// 	optional sint64 value6 = 8;
// 	optional sint64 value7 = 9;
// 	optional sint64 value8 = 10;
// 	optional sint64 value9 = 11;
// 	optional sint64 value10 = 12;
// }
//
// message NodeMessage {
//     repeated SensorReading reading = 1;
// }
export declare interface IotPayload {
  id?: number;
  error?: number;
  value1?: number;
  value2?: number;
  value3?: number;
  value4?: number;
  value5?: number;
  value6?: number;
  value7?: number;
  value8?: number;
  value9?: number;
  value10?: number;
}

// TTN Lora JSON format:
// {
//     "payload": "CgQIARgC",
//     "port": 1,
//     "counter": 8,
//     "dev_eui": "000000007FEE6E5B",
//     "metadata": [
//         {
//             "frequency": 868.5,
//             "datarate": "SF7BW125",
//             "codingrate": "4/5",
//             "gateway_timestamp": 2913536323,
//             "channel": 2,
//             "server_time": "2016-09-09T09:14:32.141349077Z",
//             "rssi": -34,
//             "lsnr": 6.2,
//             "rfchain": 1,
//             "crc": 1,
//             "modulation": "LORA",
//             "gateway_eui": "0000024B0805026F",
//             "altitude": -1,
//             "longitude": 5.26561,
//             "latitude": 52.05755
//         }
//     ]
// }

export declare interface Metadata {
  frequency?: number;
  datarate?: string;
  codingrate?: string;
  gateway_timestamp?: number;
  channel?: number;
  server_time: string; // ISO timestring
  rssi?: number;
  lsnr?: number;
  crc?: number;
  modulation?: number;
  gateway_eui?: string;
  altitude?: number;
  longitude?: number;
  latitude?: number;
}

export declare interface Message {
  payload: IotPayload[];
  port?: number;
  counter: number;
  dev_eui: string;
  metadata: Metadata;
}

export declare interface SensorObservation {
  timestamp: string; // ISO timestring
  nodeId: string;
  sensorId: number;
  sensorError: number;
  sensorValueType?: string;
  sensorValue: any;
  sensorValue2?: number;
  sensorValue3?: number;
  sensorValue4?: number;
  sensorValue5?: number;
  sensorValue6?: number;
  sensorValue7?: number;
  sensorValue8?: number;
  sensorValue9?: number;
  sensorValue10?: number;
  logId?: any;
}

export declare interface SensorAlert {
  nodeId: string;
  sensorId: number;
  sensorValue: any;
  sensorValueType: string;
  observationId: any;
  ruleId: number;
  logId?: any;
}

export declare interface AlertNotification {
  kanaal: string;
  p1: string;
  p2: string;
  p3: string;
  p4: string;
  meldingtekst: string;
}

export declare interface NodeRedEnvelope {
  payload: any;
}

/**
 * Generic abstract class to decouple the sending of a message from a particular implementation
 */
export abstract class SendMessages {
  send(msg: any) { };
}

/**
 * Implementation of SendMessage with AMQP
 */
export class SendMessagesAmqp implements SendMessages {
  private amqpExchange: amqp.Exchange;
  private inNodeRedEnvelope: boolean;

  /**
   * Create a SendMessage instance for amqp
   * @param (amqp.Exchange) amqpExchange - the amqp Exchange
   * @param (boolean) [inNodeRedEnvelope=false] - send the message in a NedeRed envelope
   */
  constructor(amqpExchange: amqp.Exchange, inNodeRedEnvelope = true) {
    this.amqpExchange = amqpExchange;
    this.inNodeRedEnvelope = inNodeRedEnvelope;
  }

  send(msg) {
    if (this.inNodeRedEnvelope) {
      var msg = <any>{
        payload: msg
      };
    }
    var amqpMsg = new amqp.Message(msg);
    this.amqpExchange.send(amqpMsg);
  }
}

/**
 * Generic abstract class to decouple the message receiving from a particular implementation
 */
export abstract class ReceiveMessages {
  startConsumer(msgReceiver: (msg: any) => void) { }
  stopConsumer(): Promise<void> { return Promise.resolve(); }
}

export class ReceiveMessagesAmqp implements ReceiveMessages {
  private amqpQueue: amqp.Queue;
  private inNodeRedEnvelope: boolean;
  private msgReceiver: (msg: any) => void;

  /**
   * Create a ReceiveMessage instance for amqp
   * @param (amqp.Queue) amqpQueue - the amqp Exchange
   * @param (boolean) [inNodeRedEnvelope=false] - send the message in a NedeRed envelope
   */
  constructor(amqpQueue: amqp.Queue, inNodeRedEnvelope = true) {
    this.amqpQueue = amqpQueue;
    this.inNodeRedEnvelope = inNodeRedEnvelope;
  }

  receiveMessage(msg: amqp.Message) {
    var content: any = msg.content.toString();
    try {
      content = JSON.parse(content);
    } catch (_) { }
    if (this.inNodeRedEnvelope) {
      content = content.payload;
    }
    this.msgReceiver(content);
  }

  startConsumer(msgReceiver: (msg: any) => void) {
    if (this.msgReceiver) {
      //todo: log error
      throw new Error("ReceiveAmqpMessages.startReceiving: receiver already started!");
    }
    this.msgReceiver = msgReceiver;
    this.amqpQueue.activateConsumer((msg) => {
      this.receiveMessage(msg);
    }, { noAck: true });
  }

  stopConsumer(): Promise<void> {
    return this.amqpQueue.stopConsumer()
      .then(() => {
        delete this.msgReceiver;
      });
  }
}



