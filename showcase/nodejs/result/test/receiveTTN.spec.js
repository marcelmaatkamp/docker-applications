/**
 * tests for ProtocolBuffer
 *
 * 2016-10-11 Ab Reitsma
 */
"use strict";
var mqtt = require("mqtt");
// import * as amqp from "amqp-ts";
var Chai = require("chai");
var expect = Chai.expect;
var iot = require("../code/iotMsg");
var receiveTTN_1 = require("../code/receiveTTN");
var ttnSupport = require("./_ttnTestSupport");
var mqttBrokerUrl = "mqtt://rabbitmq:1883";
var amqpBrokerUrl = "amqp://rabbitmq";
// initialize support
ttnSupport.SetConnectionUrl({
    mqtt: mqttBrokerUrl,
    amqp: amqpBrokerUrl
});
describe("Test ReceiveTTN", function () {
    it("should be able to connect to and disconnect from an mqtt broker", function (done) {
        var mqttClient = mqtt.connect(mqttBrokerUrl);
        mqttClient.on("connect", function () {
            mqttClient.end(true, function () {
                done();
            });
        });
        mqttClient.on("error", function (err) {
            done(err);
        });
    });
    it("should receive a ttn message", function (done) {
        var t = new ttnSupport.TtnToMessageTest(done);
        t.mqttClient.on("connect", function () {
            t.mqttClient.subscribe("#");
            t.mqttClient.publish("#", "testmessage");
        });
        t.mqttClient.on("message", function (topic, message) {
            expect(topic).to.equal("#");
            expect(message).to.deep.equal(new Buffer("testmessage"));
            t.finish();
        });
    });
    it("should process a ttn message", function (done) {
        var t = new ttnSupport.TtnToMessageTest(done, true);
        //tslint:disable-next-line:no-unused-variable
        var sender = new iot.SendMessagesAmqp(t.exchange, false);
        new receiveTTN_1.default(t.mqttClient, sender);
        t.queue.activateConsumer(function (msg) {
            try {
                var content = msg.getContent();
                expect(content).to.deep.equal(ttnExpectedResult);
                t.finish();
            }
            catch (err) {
                t.finish(err);
            }
        }, { noAck: true });
        // make sure everything is connected before sending the test message
        t.startAll()
            .then(function () {
            t.mqttClient.publish("#", JSON.stringify(ttnTestMessage));
        });
    });
});
/**
 * TTN MQTT test message and expected result
 */
var ttnTestMessage = {
    "payload": "CgQIARgC", "port": 1, "counter": 8, "dev_eui": "000000007FEE6E5B", "metadata": [{
            "frequency": 868.5, "datarate": "SF7BW125", "codingrate": "4/5", "gateway_timestamp": 2913536323, "channel": 2,
            "server_time": "2016-09-09T09:14:32.141349077Z", "rssi": -34, "lsnr": 6.2, "rfchain": 1, "crc": 1,
            "modulation": "LORA", "gateway_eui": "0000024B0805026F", "altitude": -1, "longitude": 5.26561, "latitude": 52.05755
        }]
};
var ttnExpectedResult = {
    payload: [{
            id: 1,
            error: 0,
            value1: 1,
            value2: 0,
            value3: 0,
            value4: 0,
            value5: 0,
            value6: 0,
            value7: 0,
            value8: 0,
            value9: 0,
            value10: 0
        }],
    port: 1,
    counter: 8,
    dev_eui: "000000007FEE6E5B",
    metadata: [{
            frequency: 868.5,
            datarate: "SF7BW125",
            codingrate: "4/5",
            gateway_timestamp: 2913536323,
            channel: 2,
            server_time: "2016-09-09T09:14:32.141349077Z",
            rssi: -34,
            lsnr: 6.2,
            rfchain: 1,
            crc: 1,
            modulation: "LORA",
            gateway_eui: "0000024B0805026F",
            altitude: -1,
            longitude: 5.26561,
            latitude: 52.05755
        }]
};

//# sourceMappingURL=receiveTTN.spec.js.map
