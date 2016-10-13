/**
 * process TTN and KPN Lora messages
 * Typescript alternative to the node-red flows
 *
 * 2016-10-10 Ab Reitsma
 */

import * as mqtt from "mqtt";
import * as amqp from "amqp-ts";
import * as iot from "./iotMsg";
import ProcessKPN from "./ProcessKPN";
import ProcessTTN from "./ProcessTTN";

// declare mqtt stuff (for TTN)
const ttnMqttServer = process.env.SHOWCASE_MQTT_SERVER || "staging.thethingsnetwork.org";
const TtnMqttPort = process.env.SHOWCASE_MQTT_PORT || 1883;
const ttnMqttUser = process.env.SHOWCASE_MQTT_USER;
const ttnMqttToken = process.env.SHOWCASE_MQTT_TOKEN;

//declare amqp generic stuff
var amqpServer = process.env.SHOWCASE_AMQP_PASSWORD || "rabbitmq";
var amqpPort = process.env.SHOWCASE_AMQP_PORT || 5672;
var amqpUser = process.env.SHOWCASE_AMQP_USER || "guest";
var amqpPassword = process.env.SHOWCASE_AMQP_SERVER || "guest";
var amqpQueueSuffix = process.env.SHOWCASE_AMQP_QUEUE_SUFFIX || "nodejs_queue";

var connectionUrl = "amqp://" + amqpUser + ":" + amqpPassword + "@" + amqpServer + ":" + amqpPort;
var amqpConnection = new amqp.Connection(connectionUrl);

//declare amqp exchange names
var ttnExchange = new IoConnection({
    out: process.env.SHOWCASE_AMQP_TTN_EXCHANGE_OUT || "showcase.ttn_message"
});

var kpnExchange = new IoConnection({
    in: process.env.SHOWCASE_AMQP_KPN_EXCHANGE_IN || "showcase.kpn_message",
    out: process.env.SHOWCASE_AMQP_KPN_EXCHANGE_IN || ttnExchange.out
});

var decodeExchangeNameIn = process.env.SHOWCASE_AMQP_DECODE_EXCHANGE_IN || ttnExchange.out;
var decodeExchangeNameOut = process.env.SHOWCASE_AMQP_DECODE_EXCHANGE_OUT || "showcase.observation";

var logExchangeNameIn = process.env.SHOWCASE_AMQP_LOG_EXCHANGE_IN || decodeExchangeNameOut;
var logExchangeNameOut = process.env.SHOWCASE_AMQP_LOG_EXCHANGE_IN || "showcase.logged_observation";

var processExchangeNameIn = process.env.SHOWCASE_AMQP_LOG_EXCHANGE_IN || logExchangeNameOut;
var processExchangeNameOut = process.env.SHOWCASE_AMQP_LOG_EXCHANGE_IN || "showcase.alert";

var alertExchangeNameIn = process.env.SHOWCASE_AMQP_LOG_EXCHANGE_IN || processExchangeNameOut;

//declare amqp exchange and queue topology
var ttnExchangeOut = amqpConnection.declareExchange(ttnExchangeNameOut, "fanout");

var kpnExchangeIn = amqpConnection.declareExchange(kpnExchangeNameIn, "fanout");
var kpnQueueIn = amqpConnection.declareQueue(kpnExchangeNameIn + "." + amqpQueueSuffix);
kpnQueueIn.bind(kpnExchangeIn);
var kpnExchangeOut = amqpConnection.declareExchange(kpnExchangeNameOut, "fanout");

interface IoConnectionNames {
    in?: string | amqp.Exchange;
    out?: string | amqp.Exchange;
}

class IoConnection {
    static queueNr = 1;

    in: amqp.Queue;
    out: amqp.Exchange;

    constructor (create: IoConnectionNames) {
        this.out = this.getExchange(create.out);

        var inExchange = this.getExchange(create.in);
        this.in = amqpConnection.declareQueue(inExchange + "." + amqpQueueSuffix);
    }

    private getExchange(exchange: string | amqp.Exchange) {
        if (typeof exchange === "string") {
            return amqpConnection.declareExchange(exchange);
        }
        return exchange;
    }
}

