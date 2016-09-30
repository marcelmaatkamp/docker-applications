/*
 * SHOWCASE-TESTS
 *
 * 2016-09-29 Ab Reitsma
 *
 * Programma dat test of de invoer via RabbitMQ voor ShowCase
 * overeenkomt met de verwachte uitvoer
 *
 */
"use strict";
var amqp = require("./amqp-ts.d.ts");
var testTimeout = 1000; // timeout per test in ms
var testSet = [];
var ShowcaseTest = (function () {
    function ShowcaseTest(test, connection) {
        this.test = test;
        this.connection = connection || new amqp.Connection("amqp://rabbitmq");
    }
    ShowcaseTest.prototype.checkMessage = function (msg, exchangeResults) {
        var found = false;
        var expectedMessages = exchangeResults.expectedMessages;
        for (var i = 0, len = expectedMessages.length; i < len; i++) {
            if (msg.getContent() === expectedMessages[i].result) {
                found = true;
                if (!expectedMessages[i].received) {
                    expectedMessages[i].received = true;
                    return;
                }
            }
        }
        if (found) {
        }
        else {
        }
    };
    // prepare test
    ShowcaseTest.prototype.prepareTest = function () {
        var _this = this;
        // create/connect to all exchanges
        this.exchanges[this.test.sendExchange] = this.connection.declareExchange(this.test.sendExchange);
        var results = this.test.expectedResults;
        var exchange;
        var _loop_1 = function(i, len) {
            if (!this_1.exchanges[results[i].exchange]) {
                exchange = this_1.connection.declareExchange(results[i].exchange);
                this_1.exchanges[results[i].exchange] = exchange;
            }
            exchange.activateConsumer(function (msg) {
                _this.checkMessage(msg, results[i]);
            });
        };
        var this_1 = this;
        for (var i = 0, len = results.length; i < len; i++) {
            _loop_1(i, len);
        }
        return this.connection.completeConfiguration();
    };
    ShowcaseTest.prototype.startTest = function () {
        var message = new amqp.Message(this.test.sendMessage);
        this.exchanges[this.test.sendExchange].send(message);
    };
    ShowcaseTest.prototype.finishTest = function () {
        // wait until timeout has exceeded, then check if all messages have been received
        // notify missing messages
        // return promise containing test results that resolves when tests are completed
    };
    return ShowcaseTest;
}());
// initialize tests
// TODO
// execute test
// cleanup test
// log test

//# sourceMappingURL=showcase_tests.js.map
