"use strict";
/*
 * SHOWCASE-TESTS
 *
 * 2016-09-29 Ab Reitsma
 *
 * Programma dat test of de invoer via RabbitMQ voor ShowCase
 * overeenkomt met de verwachte uitvoer
 *
 */
var Promise = require("bluebird");
var fs = require("fs");
var amqp = require("./amqp-ts.d.ts");
var testTimeout = process.env.TEST_TIMEOUT || 1000; // timeout per test in ms
var testSet = [];
var ShowcaseTest = (function () {
    function ShowcaseTest(test, connection) {
        this.success = true;
        this.test = test;
        this.connection = connection || new amqp.Connection("amqp://rabbitmq");
    }
    ShowcaseTest.prototype.checkMessage = function (msg, exchangeResults) {
        var found = false;
        var expectedMessages = exchangeResults.expectedMessages;
        for (var i_1 = 0, len = expectedMessages.length; i_1 < len; i_1++) {
            if (msg.getContent() === expectedMessages[i_1].result) {
                found = true;
                if (!expectedMessages[i_1].received) {
                    expectedMessages[i_1].received = true;
                    return;
                }
            }
        }
        this.success = false;
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
        var _loop_1 = function(i_2, len) {
            if (!this_1.exchanges[results[i_2].exchange]) {
                exchange = this_1.connection.declareExchange(results[i_2].exchange);
                this_1.exchanges[results[i_2].exchange] = exchange;
            }
            exchange.activateConsumer(function (msg) {
                _this.checkMessage(msg, results[i_2]);
            });
        };
        var this_1 = this;
        for (var i_2 = 0, len = results.length; i_2 < len; i_2++) {
            _loop_1(i_2, len);
        }
        return this.connection.completeConfiguration();
    };
    ShowcaseTest.prototype.startTest = function () {
        var message = new amqp.Message(this.test.sendMessage);
        this.exchanges[this.test.sendExchange].send(message);
    };
    ShowcaseTest.prototype.checkResults = function () {
        // check if all expected messages have been received
        var results = this.test.expectedResults;
        for (var i_3 = 0, len = results.length; i_3 < len; i_3++) {
            var messages = results[i_3].expectedMessages;
            for (var j = 0, len_1 = messages.length; j < len_1; j++) {
                if (!messages[j].received) {
                    this.success = false;
                }
            }
        }
    };
    ShowcaseTest.prototype.finishTest = function () {
        var _this = this;
        return new Promise(function (resolve, reject) {
            setTimeout(function () {
                _this.checkResults();
                resolve(_this.success);
            }, _this.test.testTimeout || testTimeout);
        });
    };
    ShowcaseTest.prototype.runTest = function () {
        return this.prepareTest()
            .then(this.startTest)
            .then(this.finishTest);
    };
    return ShowcaseTest;
}());
var testsBuffer = fs.readFileSync("../data/tests.json").toString();
var tests = JSON.parse(testsBuffer);
var errorCount = 0;
var i = 0;
function executeTest() {
    "use strict";
    var currentTest = tests[i];
    var test = new ShowcaseTest(currentTest);
    test.runTest()
        .then(function (errorsOccurred) {
        if (errorsOccurred) {
            errorCount += 1;
        }
        i += 1;
        if (i < tests.length) {
            executeTest();
        }
        else {
        }
    });
}
executeTest();

//# sourceMappingURL=showcase_tests.js.map
