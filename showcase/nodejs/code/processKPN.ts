/**
 * receive TTN messages and export them to RabbitMQ
 *
 * 2016-10-11 Ab Reitsma
 */

import * as mqtt from "mqtt";
import * as amqp from "amqp-ts";
import * as iot from "./iotMsg";
import decodeProtoBuf from "./decodeProtoBuf";


declare interface MessageKPN {
    LrrSNR: string;
    Lrrid: any;
    SpFact: number;
    SubBand: any;
    CustomerData: any;
    FPort: number;
    Channel: any;
    FCntUp: number;
    Time: any;
    DevEUI: string;
    payload_hex: string;
    CustomerID: string;
    LrrRSSI: string;
    ADRbit: number;
    ModelCfg: number;
    mic_hex: string;
    LrrLON: string;
    LrrLAT: string;
    FCntDn: number;
    Lrcid: any;
    DevLrrCnt: number;
}

export default class ProcessKPN {
  srcQueue: amqp.Queue;
  destExchange: amqp.Exchange;

  constructor (srcQueue: amqp.Queue, destExchange: amqp.Exchange) {
    this.srcQueue = srcQueue;
    this.destExchange = destExchange;

    srcQueue.activateConsumer(this.MessageConsumerKPN, {noAck: true});
  }

  private MessageConsumerKPN (msg: amqp.Message) {
    var messageRaw = msg.content;
    var messageKPN = <MessageKPN> JSON.parse(messageRaw.toString());
    var rawPayload = new Buffer(messageKPN.payload_hex, "hex");
    var payload = decodeProtoBuf(rawPayload);

    var metadata: iot.Metadata = {
      server_time: new Date().toISOString(),
      longitude: Number(messageKPN.LrrLON),
      latitude: Number(messageKPN.LrrLAT)
      // other metadata fields ignored for now
    };

    // convert payload
    var messageIot = {
      payload: payload,
      port: messageKPN.FPort,
      counter: messageKPN.FCntUp,
      dev_eui: messageKPN.DevEUI,
      metadata: metadata
    };

    // publish to exchange
    var messageAmqp = new amqp.Message(messageIot);
    this.destExchange.send(messageAmqp);
  }
}
