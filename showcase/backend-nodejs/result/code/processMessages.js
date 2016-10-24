/**
 * process TTN and KPN Lora messages
 * Typescript alternative to the node-red flows
 *
 * 2016-10-10 Ab Reitsma
 */
"use strict";
var winston = require("winston");
var mysql = require("mysql");
var mqtt = require("mqtt");
var amqp = require("amqp-ts");
var Slack = require("node-slack");
var iot = require("./iotMsg");
var receiveKPN_1 = require("./receiveKPN");
var receiveTTN_1 = require("./receiveTTN");
var decodeToObservations_1 = require("./decodeToObservations");
var logObservation_1 = require("./logObservation");
var processObservation_1 = require("./processObservation");
var processAlert_1 = require("./processAlert");
var LogAlert_1 = require("./LogAlert");
var ProcessNotificationSlack_1 = require("./ProcessNotificationSlack");
var ProcessNotificationTelegram_1 = require("./ProcessNotificationTelegram");
// define log settings
var logToGrayLog = process.env.SHOWCASE_GRAYLOG || false;
var graylogHost = process.env.SHOWCASE_GRAYLOG_HOST || "graylog";
var graylogPort = process.env.SHOWCASE_GRAYLOG_PORT || 12201;
var graylogLevel = process.env.SHOWCASE_GRAYLOG_LEVEL || "debug";
var consoleLogLevel = process.env.SHOWCASE_LOG_LEVEL || "info";
var consoleLogMeta = process.env.SHOWCASE_LOG_META || false;
winston.remove(winston.transports.Console);
winston.add(winston.transports.Console, {
    level: consoleLogLevel,
    timestamp: function () {
        return "[" + new Date().toLocaleTimeString([], { hour12: false }) + "]";
    },
    formatter: function (options) {
        return options.timestamp() + " " +
            options.level.toUpperCase() + " " +
            (options.message === undefined ? "" : options.message) +
            (consoleLogMeta && options.meta && Object.keys(options.meta).length ?
                '\n\t' + JSON.stringify(options.meta) : '');
    }
});
console.log("Winston console logging started");
if (logToGrayLog) {
    winston.add(require("winston-graylog2"), {
        name: "Graylog",
        level: graylogLevel,
        graylog: {
            servers: [{ host: graylogHost, port: graylogPort }],
            hostname: "backend"
        }
    });
    console.log("Winston graylog logging started, host: '" + graylogHost + "' port: " + graylogPort);
}
// unfortunately no typescript .d.ts exists for node-telegram-bot-api
var TelegramBot = require("node-telegram-bot-api");
// declare mysql stuff
var mysqlHost = process.env.SHOWCASE_MYSQL_HOST || "mysql";
var mysqlUser = process.env.SHOWCASE_MYSQL_USER || "root";
var mysqlPassword = process.env.SHOWCASE_MYSQL_PASSWORD || "my-secret-pw";
var mysqlDatabase = process.env.SHOWCASE_MYSQL_DATABASE || "showcase";
// create mysql connection
var mysqlDb = mysql.createConnection({
    host: mysqlHost,
    user: mysqlUser,
    password: mysqlPassword,
    database: mysqlDatabase
});
// declare mqtt stuff (for TTN)
var ttnMqttServer = process.env.SHOWCASE_MQTT_SERVER || "staging.thethingsnetwork.org";
var ttnMqttPort = process.env.SHOWCASE_MQTT_PORT || 1883;
var ttnMqttUser = process.env.SHOWCASE_MQTT_USER;
var ttnMqttPassword = process.env.SHOWCASE_MQTT_PASSWORD;
// create mqtt connection
var mqttClient = mqtt.connect("mqtt://" + ttnMqttServer + ":" + ttnMqttPort, {
    username: ttnMqttUser,
    password: ttnMqttPassword,
    protocolVersion: 4,
    keepalive: 60,
    clean: true
});
mqttClient.on("error", function (err) {
    winston.error("An MQTT error occurred: " + err.message);
});
mqttClient.on("offline", function (err) {
    winston.warn("TTN MQTT server offline.");
});
mqttClient.on("reconnect", function (err) {
    winston.warn("TTN MQTT server reconnect started.");
});
mqttClient.on("close", function (err) {
    winston.warn("TTN MQTT server connection closed.");
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
var slackBot = new Slack(slackHookUrl);
// create telegram connection
var telegramBotToken = process.env.SHOWCASE_TELEGRAM_TOKEN ||
    "292441232:AAHS3zE8dyJWRUCx29bLx-MOwWEpimRt0mk";
var telegramBot = new TelegramBot(telegramBotToken);
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
    in: process.env.SHOWCASE_AMQP_NOTIFICATION_EXCHANGE_IN || alertAmqp.outExchange,
    out: process.env.SHOWCASE_AMQP_NOTIFICATION_EXCHANGE_OUT || "showcase.logged_notification"
});
var notificationSlackAmqp = new iot.AmqpInOut({
    in: process.env.SHOWCASE_AMQP_SLACK_EXCHANGE_IN || alertAmqp.outExchange,
    out: process.env.SHOWCASE_AMQP_SLACK_EXCHANGE_OUT || "showcase.notification_sent_slack"
});
var notificationTelegramAmqp = new iot.AmqpInOut({
    in: process.env.SHOWCASE_AMQP_TELKEGRAM_EXCHANGE_IN || alertAmqp.outExchange,
    out: process.env.SHOWCASE_AMQP_TELEGRAM_EXCHANGE_OUT || "showcase.notification_sent_telegram"
});
// create and start the message processing elements
new receiveTTN_1.default(mqttClient, ttnAmqp.send);
new receiveKPN_1.default(kpnAmqp.receive, kpnAmqp.send);
new decodeToObservations_1.default(decodeAmqp.receive, decodeAmqp.send, mysqlDb);
new logObservation_1.default(logObservationAmqp.receive, logObservationAmqp.send, mysqlDb);
new processObservation_1.default(processAmqp.receive, processAmqp.send, mysqlDb);
new processAlert_1.default(alertAmqp.receive, alertAmqp.send, mysqlDb);
new LogAlert_1.default(alertlogLogAmqp.receive, alertlogLogAmqp.send, mysqlDb);
new ProcessNotificationSlack_1.default(notificationSlackAmqp.receive, notificationSlackAmqp.send, slackBot);
new ProcessNotificationTelegram_1.default(notificationTelegramAmqp.receive, notificationTelegramAmqp.send, telegramBot);

//# sourceMappingURL=processMessages.js.map
