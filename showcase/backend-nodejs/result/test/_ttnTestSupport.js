/**
 * testToolsTTN.ts
 *
 * omschrijving
 *
 */
"use strict";
var mqtt = require("mqtt");
var amqp = require("amqp-ts");
var Promise = require("bluebird");
var amqpSupport = require("./_amqpIoTestSupport");
var mqttBrokerUrl;
var amqpBrokerUrl;
function SetConnectionUrl(urls) {
    mqttBrokerUrl = urls.mqtt;
    amqpBrokerUrl = urls.amqp;
}
exports.SetConnectionUrl = SetConnectionUrl;
// self containing test and cleanup support class
var TtnToMessageTest = (function () {
    function TtnToMessageTest(done, createQueue) {
        var _this = this;
        this.doneSent = false;
        this.mqttFinished = false;
        this.amqpFinished = false;
        this.testNr = amqpSupport.TestNumber.getTestNr();
        this.done = done;
        this.mqttClient = mqtt.connect(mqttBrokerUrl);
        this.amqpConnection = new amqp.Connection(amqpBrokerUrl);
        this.exchange = this.amqpConnection.declareExchange("test_" + this.testNr + ".showcase.ttnexchange", "fanout", { durable: false });
        this.mqttClient.on("error", function (err) {
            _this.finish(err);
        });
        this.mqttClient.on("close", function (err) {
            _this.mqttFinished = true;
            _this.allFinished();
        });
        if (createQueue) {
            this.queue = this.amqpConnection.declareQueue("test_" + this.testNr + ".showcase.ttnqueue", { durable: false });
            this.queue.bind(this.exchange);
        }
    }
    TtnToMessageTest.prototype.allFinished = function () {
        if (this.mqttFinished && this.amqpFinished && !this.doneSent) {
            this.doneSent = true;
            this.done(this.errorMessage);
        }
    };
    TtnToMessageTest.prototype.finish = function (err) {
        var _this = this;
        this.errorMessage = err;
        this.amqpConnection.completeConfiguration()
            .then(function () {
            _this.amqpConnection.deleteConfiguration()
                .then(function () {
                _this.amqpConnection.close();
            });
        })
            .then(function () {
            _this.amqpFinished = true;
            _this.allFinished();
        })
            .catch(function (err) {
            _this.errorMessage = err;
            _this.allFinished();
        });
        this.mqttClient.end(true);
    };
    TtnToMessageTest.prototype.startAll = function () {
        var _this = this;
        return new Promise(function (resolve, reject) {
            _this.mqttClient.on("connect", function () {
                _this.amqpConnection.completeConfiguration()
                    .then(function () {
                    resolve();
                })
                    .catch(function (err) {
                    reject(err);
                });
            });
        });
    };
    return TtnToMessageTest;
}());
exports.TtnToMessageTest = TtnToMessageTest;

//# sourceMappingURL=_ttnTestSupport.js.map
