/**
 * tests for ReceiveKPN
 *
 * 2016-10-11 Ab Reitsma
 */
"use strict";
require("./_logSettings");
var amqp = require("amqp-ts");
var Chai = require("chai");
var expect = Chai.expect;
var iot = require("../code/iotMsg");
var receiveKPN_1 = require("../code/receiveKPN");
var amqpSupport = require("./_amqpIoTestSupport");
var amqpBrokerUrl = "amqp://rabbitmq";
// initialize support
amqpSupport.SetConnectionUrl({
    amqp: amqpBrokerUrl
});
describe("Test ReceiveKPN", function () {
    it("should process a kpn message", function (done) {
        var t = new amqpSupport.AmqpIoTest(done, true);
        //tslint:disable-next-line:no-unused-variable
        var sender = new iot.SendMessagesAmqp(t.outExchange, false);
        var receiver = new iot.ReceiveMessagesAmqp(t.inQueue, false);
        new receiveKPN_1.default(receiver, sender);
        t.outQueue.activateConsumer(function (msg) {
            try {
                var content = msg.getContent();
                expect(content).to.deep.equal(kpnExpectedResult);
                t.finish();
            }
            catch (err) {
                t.finish(err);
            }
        }, { noAck: true });
        // make sure everything is connected before sending the test message
        t.startAll()
            .then(function () {
            var msg = new amqp.Message(kpnTestMessage);
            t.inQueue.send(msg);
        });
    });
});
/**
 * KPN test message and expected result
 */
var kpnTestMessage = { "LrrSNR": "-7.500000", "Lrrid": "080E035E", "SpFact": 7, "SubBand": "G0", "CustomerData": "{\"alr\":{\"pro\":\"SMTC/LoRaMote\",\"ver\":\"1\"}}", "FPort": 1, "Channel": "LC2", "FCntUp": 2, "Time": 1475501410829, "DevEUI": "0059AC000018041B", "payload_hex": "0a0408011802", "CustomerID": 100006356, "LrrRSSI": "-119.000000", "ADRbit": 0, "ModelCfg": 0, "mic_hex": "f402dfd2", "LrrLON": "5.304723", "LrrLAT": "52.085842", "FCntDn": 6, "Lrcid": "0059AC01", "DevLrrCnt": 1, "query": { "LrnDevEui": "0059AC000018041B", "LrnFPort": "1", "LrnInfos": "null", "AS_ID": "Politie.developer", "Time": "2016-10-03T13:30:11.155Z", "Token": "4fdc65a53d5057aee8f9a17cfd66e3a18106d5926131974b5e9d3501d97d11e0" } };
var kpnExpectedResult = {
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
    counter: 2,
    dev_eui: "0059AC000018041B",
    metadata: [{
            server_time: "2016-10-03T13:30:10.829Z",
            longitude: 5.304723,
            latitude: 52.085842
        }]
};

//# sourceMappingURL=receiveKPN.spec.js.map
