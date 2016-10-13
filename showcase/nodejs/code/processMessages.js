/**
 * process TTN and KPN Lora messages
 * Typescript alternative to the node-red flows
 *
 * 2016-10-10 Ab Reitsma
 */
"use strict";
var amqp = require("amqp-ts");
// declare mqtt stuff (for TTN)
var ttnMqttServer = process.env.SHOWCASE_MQTT_SERVER || "staging.thethingsnetwork.org";
var TtnMqttPort = process.env.SHOWCASE_MQTT_PORT || 1883;
var ttnMqttUser = process.env.SHOWCASE_MQTT_USER;
var ttnMqttToken = process.env.SHOWCASE_MQTT_TOKEN;
//declare amqp generic stuff
var amqpServer = process.env.SHOWCASE_AMQP_PASSWORD || "rabbitmq";
var amqpPort = process.env.SHOWCASE_AMQP_PORT || 5672;
var amqpUser = process.env.SHOWCASE_AMQP_USER || "guest";
var amqpPassword = process.env.SHOWCASE_AMQP_SERVER || "guest";
var amqpQueueSuffix = process.env.SHOWCASE_AMQP_QUEUE_SUFFIX || "nodejs_queue";
var connectionUrl = "amqp://" + amqpUser + ":" + amqpPassword + "@" + amqpServer + ":" + amqpPort;
var amqpConnection = new amqp.Connection(connectionUrl);
//declare amqp exchange names
var ttnExchangeNameOut = process.env.SHOWCASE_AMQP_TTN_EXCHANGE_OUT || "showcase.ttn_message";
var kpnExchangeNameIn = process.env.SHOWCASE_AMQP_KPN_EXCHANGE_IN || "showcase.kpn_message";
var kpnExchangeNameOut = process.env.SHOWCASE_AMQP_KPN_EXCHANGE_IN || ttnExchangeNameOut;
var decodeExchangeNameIn = process.env.SHOWCASE_AMQP_DECODE_EXCHANGE_IN || ttnExchangeNameOut;
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

//# sourceMappingURL=processMessages.js.map
