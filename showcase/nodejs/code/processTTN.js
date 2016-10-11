/**
 * receive TTN messages and export them to RabbitMQ
 *
 * 2016-10-10 Ab Reitsma
 */
var ttn = require("ttn"); // no typings available
// setup ttn credentials
var region = "eu";
var appId = process.env.SHOWCASE_APPID;
var accessKey = process.env.SHOWCASE_ACCESSKEY;
var ttnClient = new ttn.Client(region, appId, accessKey);
//todo: create working code from example
ttnClient.on("connect", function (connack) {
    console.log("[DEBUG]", "Connect:", connack);
});
ttnClient.on("error", function (err) {
    console.error("[ERROR]", err.message);
});
ttnClient.on("activation", function (deviceId, data) {
    console.log("[INFO] ", "Activation:", deviceId, JSON.stringify(data, null, 2));
});
ttnClient.on("device", null, "down/scheduled", function (deviceId, data) {
    console.log("[INFO] ", "Scheduled:", deviceId, JSON.stringify(data, null, 2));
});
ttnClient.on("message", function (deviceId, data) {
    console.info("[INFO] ", "Message:", deviceId, JSON.stringify(data, null, 2));
});
ttnClient.on("message", null, "led", function (deviceId, led) {
    // Toggle the LED
    var payload = {
        led: !led
    };
    // If you don"t have an encoder payload function:
    // var payload = [led ? 0 : 1];
    console.log("[DEBUG]", "Sending:", JSON.stringify(payload));
    ttnClient.send(deviceId, payload);
});

//# sourceMappingURL=processTTN.js.map
