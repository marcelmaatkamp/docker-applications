/**
 * process TTN and KPN Lora messages
 * Typescript alternative to the node-red flows
 *
 * 2016-10-10 Ab Reitsma
 */

import * as mysql from "mysql";
import * as mqtt from "mqtt";
import * as amqp from "amqp-ts";
import * as Slack from "node-slack";
import * as iot from "./iotMsg";
import ReceiveKPN from "./receiveKPN";
import ReceiveTTN from "./receiveTTN";
import DecodeToObservations from "./decodeToObservations";
import LogObservation from "./logObservation";
import ProcessObservation from "./processObservation";
import ProcessAlert from "./processAlert";
import LogAlert from "./LogAlert";
import ProcessNotificationSlack from "./ProcessNotificationSlack";

// declare mysql stuff
const mysqlHost = process.env.SHOWCASE_MYSQL_HOST || "mysql";
const mysqlUser = process.env.SHOWCASE_MYSQL_USER || "root";
const mysqlPassword = process.env.SHOWCASE_MYSQL_PASSWORD || "my-secret-pw";
const mysqlDatabase = process.env.SHOWCASE_MYSQL_DATABASE || "showcase";

// create mysql connection
var mysqlDb = mysql.createConnection({
  host: mysqlHost,
  user: mysqlUser,
  password: mysqlPassword,
  database: mysqlDatabase
});

// declare mqtt stuff (for TTN)
const ttnMqttServer = process.env.SHOWCASE_MQTT_SERVER || "staging.thethingsnetwork.org";
const ttnMqttPort = process.env.SHOWCASE_MQTT_PORT || 1883;
const ttnMqttUser = process.env.SHOWCASE_MQTT_USER;
const ttnMqttPassword = process.env.SHOWCASE_MQTT_PASSWORD;

// create mqtt connection
var mqttClient = mqtt.connect("mqtt://" + ttnMqttServer + ":" + ttnMqttPort, {
  username: ttnMqttUser,
  password: ttnMqttPassword,
  protocolVersion: 4,
  keepalive: 60,
  clean: true
});
mqttClient.on("error", (err) => {
  console.log("An MQTT error occurred: " + err.message);
});
mqttClient.on("offline", (err) => {
  console.log("TTN MQTT server offline.");
});
mqttClient.on("reconnect", (err) => {
  console.log("TTN MQTT server reconnect started.");
});
mqttClient.on("close", (err) => {
  console.log("TTN MQTT server connection closed.");
});

// declare amqp generic stuff
var amqpServer = process.env.SHOWCASE_AMQP_SERVER || "rabbitmq";
var amqpPort = process.env.SHOWCASE_AMQP_PORT || 5672;
var amqpUser = process.env.SHOWCASE_AMQP_USER || "guest";
var amqpPassword = process.env.SHOWCASE_AMQP_PASSWORD || "guest";
var amqpQueueSuffix = process.env.SHOWCASE_AMQP_QUEUE_SUFFIX || "nodejs_queue_";

// create amqp connection
var connectionUrl = "amqp://" + amqpUser + ":" + amqpPassword + "@" + amqpServer + ":" + amqpPort;
var amqpConnection = new amqp.Connection(connectionUrl);

iot.AmqpInOut.preInitialize(amqpConnection, amqpQueueSuffix);

// create slack connection
var slackHookUrl = process.env.SHOWCASE_SLACK_HOOK_URL ||
  "https://hooks.slack.com/services/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";
var slack = new Slack(slackHookUrl);

// declare amqp exchange names
var ttnAmqp = new iot.AmqpInOut({
  out: process.env.SHOWCASE_AMQP_TTN_EXCHANGE_OUT || "showcase.ttn_message"
});
var kpnAmqp = new iot.AmqpInOut({
  in: process.env.SHOWCASE_AMQP_KPN_EXCHANGE_IN || "showcase.kpn_message",
  out: process.env.SHOWCASE_AMQP_KPN_EXCHANGE_OUT || ttnAmqp.outExchange
});
var decodeAmqp = new iot.AmqpInOut({
  in: process.env.SHOWCASE_AMQP_DECODE_EXCHANGE_IN || ttnAmqp.outExchange,
  out: process.env.SHOWCASE_AMQP_DECODE_EXCHANGE_OUT || "showcase.observation"
});
var logObservationAmqp = new iot.AmqpInOut({
  in: process.env.SHOWCASE_AMQP_OBSERVATION_LOG_EXCHANGE_IN || decodeAmqp.outExchange,
  out: process.env.SHOWCASE_AMQP_OBSERVATION_LOG_EXCHANGE_OUT || "showcase.logged_observation"
});
var processAmqp = new iot.AmqpInOut({
  in: process.env.SHOWCASE_AMQP_ALERT_EXCHANGE_IN || logObservationAmqp.outExchange,
  out: process.env.SHOWCASE_AMQP_ALERT_EXCHANGE_OUT || "showcase.alert"
});
var alertAmqp = new iot.AmqpInOut({
  in: process.env.SHOWCASE_AMQP_ALERT_LOG_EXCHANGE_IN || processAmqp.outExchange,
  out: process.env.SHOWCASE_AMQP_ALERT_LOG_EXCHANGE_OUT || "showcase.notification"
});
var alertlogLogAmqp = new iot.AmqpInOut({
  in: process.env.SHOWCASE_AMQP_ALERT_LOG_EXCHANGE_IN || alertAmqp.outExchange,
  out: process.env.SHOWCASE_AMQP_ALERT_LOG_EXCHANGE_OUT || "showcase.logged_notification"
});
var notificationSlackAmqp = new iot.AmqpInOut({
  in: process.env.SHOWCASE_AMQP_SLACK_EXCHANGE_IN || alertAmqp.outExchange,
  out: process.env.SHOWCASE_AMQP_SLACK_EXCHANGE_OUT || "showcase.notification_sent_slack"
});

// create and start the message processing elements
new ReceiveTTN(mqttClient, ttnAmqp.send);
new ReceiveKPN(kpnAmqp.receive, kpnAmqp.send);
new DecodeToObservations(decodeAmqp.receive, decodeAmqp.send, mysqlDb);
new LogObservation(logObservationAmqp.receive, logObservationAmqp.send, mysqlDb);
new ProcessObservation(processAmqp.receive, processAmqp.send, mysqlDb);
new ProcessAlert(alertAmqp.receive, alertAmqp.send, mysqlDb);
new LogAlert(alertlogLogAmqp.receive, alertlogLogAmqp.send, mysqlDb);
new ProcessNotificationSlack(notificationSlackAmqp.receive, notificationSlackAmqp.send, slack);
