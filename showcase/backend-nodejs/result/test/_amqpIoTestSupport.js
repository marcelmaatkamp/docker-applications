/**
 * _AmqpIoTest.ts
 *
 * omschrijving
 *
 */
"use strict";
var amqp = require("amqp-ts");
var amqpBrokerUrl;
function SetConnectionUrl(urls) {
    amqpBrokerUrl = urls.amqp;
}
exports.SetConnectionUrl = SetConnectionUrl;
var TestNumber = (function () {
    function TestNumber() {
    }
    TestNumber.getTestNr = function () {
        return TestNumber._testNr++;
    };
    // create a unique number for each test to prevent tests from influencing each other
    TestNumber._testNr = 1;
    return TestNumber;
}());
exports.TestNumber = TestNumber;
// self containing test and cleanup support class
var AmqpIoTest = (function () {
    function AmqpIoTest(done, createQueue) {
        this.testNr = TestNumber.getTestNr();
        this.done = done;
        this.amqpConnection = new amqp.Connection(amqpBrokerUrl);
        this.inQueue = this.amqpConnection.declareQueue("test_" + this.testNr + ".showcase.inqueue", { durable: false });
        this.outExchange = this.amqpConnection.declareExchange("test_" + this.testNr + ".showcase.outexchange" + this.testNr, "fanout", { durable: false });
        if (createQueue) {
            this.outQueue = this.amqpConnection.declareQueue("test_" + this.testNr + ".showcase.outqueue", { durable: false });
            this.outQueue.bind(this.outExchange);
        }
    }
    AmqpIoTest.prototype.finish = function (err) {
        var _this = this;
        this.amqpConnection.completeConfiguration()
            .then(function () {
            return _this.amqpConnection.deleteConfiguration();
        })
            .then(function () {
            return _this.amqpConnection.close();
        })
            .then(function () {
            _this.done(err);
        })
            .catch(function (err) {
            _this.done(err);
        });
    };
    AmqpIoTest.prototype.startAll = function () {
        return this.amqpConnection.completeConfiguration();
    };
    return AmqpIoTest;
}());
exports.AmqpIoTest = AmqpIoTest;

//# sourceMappingURL=_amqpIoTestSupport.js.map
