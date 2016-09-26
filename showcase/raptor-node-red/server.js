var http = require('http');
var express = require("express");
var RED = require("node-red");
var fs = require("fs");

// read nodered flows
var flows = JSON.parse(fs.readFileSync("/node-red-data/flows_nodered.json", "utf8"));
var cred = JSON.parse(fs.readFileSync("/node-red-data/flows_nodered_cred.json", "utf8"));
var credChanged  = false;

function getFlowId(typeName) {
    for (var i = 0, len = flows.length; i < len; i++) {
        if (flows[i].type === typeName) {
            return flows[i].id;
        }
    }
    return null;
}

// change mqtt credentials
var mqttUser = process.env.SHOWCASE_MQTT_USER;
var mqttPassword = process.env.SHOWCASE_MQTT_PASSWORD;
if (mqttUser !== undefined && mqttPassword !== undefined) {
    // console.log ("Using MQTT user: " + mqttUser + " and password: " + mqttPassword);
    var mqttCred = cred[getFlowId("mqtt-broker")];
    if (mqttCred) {
        mqttCred.user = mqttUser;
        mqttCred.password = mqttPassword;
        credChanged = true;
    }
}

// change telegram credentials
var telegramToken = process.env.SHOWCASE_TELEGRAM_TOKEN;
if (telegramToken !== undefined) {
    // console.log("Using Telegram token " + telegramToken);
    var telegramCred = cred[getFlowId("telegram bot")];
    if (telegramCred) {
        telegramCred.token = telegramToken;
        credChanged = true;
    }
}

// save updated credentials
if (credChanged) {
    // console.log("Writing credentials: " + JSON.stringify(cred));
    fs.writeFileSync("/node-red-data/flows_nodered_cred.json", JSON.stringify(cred),"utf8");
}

// Create an Express app
var app = express();

// Add a simple route for static content served from 'public'
app.use("/",express.static("public"));

// Create a server
var server = http.createServer(app);

// Create the settings object - see default settings.js file for other options
var settings = {
    httpAdminRoot: "/",
    httpNodeRoot: "/",
    userDir: "/node-red-data/",
    functionGlobalContext: {
      safeEval: require("safe-eval"),
      protoBuf: require("protocol-buffers"),
      fs: require("fs")
     },    // enables global context
    // enable verbose output (command line -v option)
    verbose: true,
    // Configure the logging output
    logging: {
        // Only console logging is currently supported
        console: {
            // Level of logging to be recorded. Options are:
            // fatal - only those errors which make the application unusable should be recorded
            // error - record errors which are deemed fatal for a particular request + fatal errors
            // warn - record problems which are non fatal + errors + fatal errors
            // info - record information about the general running of the application + warn + error + fatal errors
            // debug - record information which is more verbose than info + info + warn + error + fatal errors
            // trace - record very detailed logging + debug + info + warn + error + fatal errors
            level: "none",

            // Whether or not to include metric events in the log output
            metrics: false,
            // Whether or not to include audit events in the log output
            audit: false
        }
    }
};

// Initialise the runtime with a server and settings
RED.init(server, settings);

// Serve the editor UI from /red
app.use(settings.httpAdminRoot, RED.httpAdmin);

// Serve the http nodes UI from /api
app.use(settings.httpNodeRoot, RED.httpNode);

server.listen(1880);

// Start the runtime
RED.start();
