/**
 * process TTN and KPN Lora messages
 * Typescript alternative to the node-red flows
 *
 * 2016-10-10 Ab Reitsma
 */
"use strict";
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
// declare mysql stuff
var mysqlHost = process.env.SHOWCASE_MYSQL_HOST || "mysql";
var mysqlUser = process.env.SHOWCASE_MYSQL_HOST || "root";
var mysqlPassword = process.env.SHOWCASE_MYSQL_HOST || "my-secret-pw";
var mysqlDatabase = process.env.SHOWCASE_MYSQL_HOST || "showcase";
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
var ttnMqttToken = process.env.SHOWCASE_MQTT_TOKEN;
// create mqtt connection
var mqttClient = mqtt.connect("mqtt://" + ttnMqttServer + ":" + ttnMqttPort, {
    username: ttnMqttUser,
    password: ttnMqttToken,
    protocolVersion: 3.1,
    keepalive: 60,
    clean: true
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
// create slack connection
var slackHookUrl = process.env.SHOWCASE_SLACK_HOOK_URL ||
    "https://hooks.slack.com/services/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";
var slack = new Slack(slackHookUrl);
// declare amqp exchange names
var ttnAmqp = new AmqpInOut({
    out: process.env.SHOWCASE_AMQP_TTN_EXCHANGE_OUT || "showcase.ttn_message"
});
var kpnAmqp = new AmqpInOut({
    in: process.env.SHOWCASE_AMQP_KPN_EXCHANGE_IN || "showcase.kpn_message",
    out: process.env.SHOWCASE_AMQP_KPN_EXCHANGE_OUT || ttnAmqp.outExchange
});
var decodeAmqp = new AmqpInOut({
    in: process.env.SHOWCASE_AMQP_DECODE_EXCHANGE_IN || ttnAmqp.outExchange,
    out: process.env.SHOWCASE_AMQP_DECODE_EXCHANGE_OUT || "showcase.observation"
});
var logObservationAmqp = new AmqpInOut({
    in: process.env.SHOWCASE_AMQP_OBSERVATION_LOG_EXCHANGE_IN || decodeAmqp.outExchange,
    out: process.env.SHOWCASE_AMQP_OBSERVATION_LOG_EXCHANGE_OUT || "showcase.logged_observation"
});
var processAmqp = new AmqpInOut({
    in: process.env.SHOWCASE_AMQP_ALERT_EXCHANGE_IN || logObservationAmqp.outExchange,
    out: process.env.SHOWCASE_AMQP_ALERT_EXCHANGE_OUT || "showcase.alert"
});
var alertAmqp = new AmqpInOut({
    in: process.env.SHOWCASE_AMQP_ALERT_LOG_EXCHANGE_IN || processAmqp.outExchange,
    out: process.env.SHOWCASE_AMQP_ALERT_LOG_EXCHANGE_OUT || "showcase.notification"
});
var alertlogLogAmqp = new AmqpInOut({
    in: process.env.SHOWCASE_AMQP_ALERT_LOG_EXCHANGE_IN || alertAmqp.outExchange,
    out: process.env.SHOWCASE_AMQP_ALERT_LOG_EXCHANGE_OUT || "showcase.logged_notification"
});
var notificationSlackAmqp = new AmqpInOut({
    in: process.env.SHOWCASE_AMQP_SLACK_EXCHANGE_IN || alertAmqp.outExchange,
    out: process.env.SHOWCASE_AMQP_SLACK_EXCHANGE_OUT || "showcase.notification_sent_slack"
});
// create and start the message processing elements
new receiveTTN_1.default(mqttClient, ttnAmqp.send);
new receiveKPN_1.default(kpnAmqp.receive, kpnAmqp.send);
new decodeToObservations_1.default(decodeAmqp.receive, decodeAmqp.send, mysqlDb);
new logObservation_1.default(logObservationAmqp.receive, logObservationAmqp.send, mysqlDb);
new processObservation_1.default(processAmqp.receive, processAmqp.send, mysqlDb);
new processAlert_1.default(alertAmqp.receive, alertAmqp.send, mysqlDb);
new LogAlert_1.default(alertlogLogAmqp.receive, alertlogLogAmqp.send, mysqlDb);
new ProcessNotificationSlack_1.default(notificationSlackAmqp.receive, notificationSlackAmqp.send, slack);
var AmqpInOut = (function () {
    function AmqpInOut(create) {
        this.outExchange = this.getExchange(create.out);
        this.send = new iot.SendMessagesAmqp(this.outExchange);
        var inExchange = this.getExchange(create.in);
        if (inExchange) {
            this.inQueue = amqpConnection.declareQueue(inExchange.name + "." + amqpQueueSuffix + this.nextQueueNr(), { durable: false });
            this.inQueue.bind(inExchange);
            this.receive = new iot.ReceiveMessagesAmqp(this.inQueue);
        }
    }
    AmqpInOut.prototype.nextQueueNr = function () {
        return AmqpInOut._queueNr++;
    };
    AmqpInOut.prototype.getExchange = function (exchange) {
        if (typeof exchange === "string") {
            return amqpConnection.declareExchange(exchange, "fanout");
        }
        // expect it to be an amqp.Exchange or null
        return exchange;
    };
    AmqpInOut._queueNr = 1;
    return AmqpInOut;
}());

//# sourceMappingURL=processMessages.js.map
