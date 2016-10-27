/**
 * receive TTN messages and export them to RabbitMQ
 *
 * 2016-10-11 Ab Reitsma
 */

import * as winston from "winston";
import * as mqtt from "mqtt";
import * as iot from "./iotMsg";
import decodeProtoBuf from "./decodeProtobuf";

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
declare interface MessageTTN {
  payload: string;
  port: number;
  counter: number;
  dev_eui: string;
  metadata: iot.Metadata;
}

export default class ReceiveKPN {
  mqttClient: mqtt.Client;
  sender: iot.SendMessages;

  constructor (ttnMQTT: mqtt.Client, sender: iot.SendMessages) {
    this.mqttClient = ttnMQTT;
    this.sender = sender;

    // initialize MQTT message receive
    ttnMQTT.on("connect", () => {
      winston.info("Connected to the TTN MQTT exchange.");
      ttnMQTT.subscribe("#");
    });
    ttnMQTT.on("message", (topic, message) => {
      winston.debug("TTN message received.", message);
      this.messageConsumerMQTT(topic, message);
    });
  }

  private messageConsumerMQTT (topic, messageRaw: Buffer) {
    var messageTTN = <MessageTTN> JSON.parse(messageRaw.toString());
    var rawPayload = new Buffer(messageTTN.payload, "base64");
    var payload = decodeProtoBuf(rawPayload);

    // convert payload
    var messageIot = {
      payload: payload,
      port: messageTTN.port,
      counter: messageTTN.counter,
      dev_eui: messageTTN.dev_eui,
      metadata: messageTTN.metadata
    };

    // publish
    this.sender.send(messageIot);
  }
}
