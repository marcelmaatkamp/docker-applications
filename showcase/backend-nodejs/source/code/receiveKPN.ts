/**
 * receive TTN messages and export them to RabbitMQ
 *
 * 2016-10-11 Ab Reitsma
 */

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

export default class ReceiveKPN {
  receiver: iot.ReceiveMessages;
  sender: iot.SendMessages;

  constructor(receiver: iot.ReceiveMessages, sender: iot.SendMessages) {
    this.receiver = receiver;
    this.sender = sender;

    receiver.startConsumer((msg) => {
      this.messageConsumerKPN(msg);
    });
  }

  private messageConsumerKPN(msg) {
    try {
      var rawPayload = new Buffer(msg.payload_hex, "hex");
      var payload = decodeProtoBuf(rawPayload);

      var metadata: iot.Metadata = {
        server_time: new Date(msg.Time).toISOString(),
        longitude: Number(msg.LrrLON),
        latitude: Number(msg.LrrLAT)
        // other metadata fields ignored for now
      };

      // convert payload
      var messageIot = {
        payload: payload,
        port: msg.FPort,
        counter: msg.FCntUp,
        dev_eui: msg.DevEUI,
        metadata: metadata
      };

      // publish result
      this.sender.send(messageIot);
    } catch (err) {
      console.log(err);
    }
  }
}
