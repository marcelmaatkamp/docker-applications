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
var fs = require("fs");
var path = require("path");
var amqp = require("amqp-ts");
var Promise = require("bluebird");
var deepEqual = require("deep-equal");
var testTimeout = process.env.TEST_TIMEOUT || 1000; // timeout per test in ms
var testSet = [];
var ShowcaseTest = (function () {
    function ShowcaseTest(test, connection) {
        this.exchanges = {};
        this.consumers = [];
        this.success = true;
        this.completed = false;
        this.test = test;
        this.connection = connection || new amqp.Connection("amqp://rabbitmq");
    }
    ShowcaseTest.prototype.startExchangeConsumer = function (exchange, exchangeResults) {
        var _this = this;
        ShowcaseTest.testQueue += 1;
        var queue = this.connection.declareQueue(ShowcaseTest.testQueuePrefix + ShowcaseTest.testQueue, { durable: false });
        queue.bind(exchange);
        var result = queue.startConsumer(function (msg) {
            _this.checkMessage(msg, exchangeResults);
        });
        this.consumers.push(queue);
    };
    ShowcaseTest.prototype.cleanupConsumers = function () {
        var _this = this;
        // cleanup current consumers
        var await = [];
        var _loop_1 = function(i_1, len) {
            await.push(this_1.consumers[i_1].stopConsumer().then(function () {
                return _this.consumers[i_1].delete();
            }));
        };
        var this_1 = this;
        for (var i_1 = 0, len = this.consumers.length; i_1 < len; i_1++) {
            _loop_1(i_1, len);
        }
        return Promise.all(await);
    };
    ShowcaseTest.prototype.checkMessage = function (msg, exchangeResults) {
        if (this.completed) {
            return;
        } // ignore messages sent after test finish
        var found = false;
        var expectedMessages = exchangeResults.expectedMessages;
        for (var i_2 = 0, len = expectedMessages.length; i_2 < len; i_2++) {
            if (deepEqual(expectedMessages[i_2].result, msg)) {
                found = true;
                if (!expectedMessages[i_2].received) {
                    expectedMessages[i_2].received = true;
                    return;
                }
            }
        }
        this.success = false;
        if (found) {
            // todo: log message received too many times
            console.log("Message received too many times: " + JSON.stringify(msg.getContent()));
        }
        else {
            // todo: log unexpected message received
            console.log("Unexpected message received: " + JSON.stringify(msg.getContent()));
        }
    };
    // prepare test
    ShowcaseTest.prototype.prepareTest = function () {
        // create/connect to all exchanges
        this.exchanges[this.test.sendExchange] = this.connection.declareExchange(this.test.sendExchange, this.test.sendExchangeType);
        var results = this.test.expectedResults;
        var exchange;
        for (var i_3 = 0, len = results.length; i_3 < len; i_3++) {
            if (!this.exchanges[results[i_3].exchange]) {
                exchange = this.connection.declareExchange(results[i_3].exchange, results[i_3].exchangeType);
                this.exchanges[results[i_3].exchange] = exchange;
                this.startExchangeConsumer(exchange, results[i_3]);
            }
        }
        return this.connection.completeConfiguration();
    };
    ShowcaseTest.prototype.startTest = function () {
        var message = new amqp.Message(this.test.sendMessage);
        this.exchanges[this.test.sendExchange].send(message);
    };
    ShowcaseTest.prototype.checkResults = function () {
        // we are finished
        this.completed = true;
        // check if all expected messages have been received
        var results = this.test.expectedResults;
        for (var i_4 = 0, len = results.length; i_4 < len; i_4++) {
            var messages = results[i_4].expectedMessages;
            for (var j = 0, len_1 = messages.length; j < len_1; j++) {
                if (!messages[j].received) {
                    this.success = false;
                    // todo: log expected result not received
                    console.log("Expected result not received: " + JSON.stringify(messages[j].result));
                }
            }
        }
        return this.success;
    };
    ShowcaseTest.prototype.finishTest = function () {
        var _this = this;
        return new Promise(function (resolve, reject) {
            setTimeout(function () {
                _this.cleanupConsumers()
                    .then(function () {
                    resolve(_this.checkResults());
                });
            }, _this.test.testTimeout || testTimeout);
        });
    };
    ShowcaseTest.prototype.runTest = function () {
        var _this = this;
        this.prepareTest()
            .then(function () {
            _this.startTest();
        });
        return this.finishTest();
    };
    ShowcaseTest.testQueuePrefix = "showcase.test_queue_";
    ShowcaseTest.testQueue = 0;
    return ShowcaseTest;
}());
var testfileLocation = path.join(__dirname, "..", "data", "tests.json");
var amqpConnection = new amqp.Connection("amqp://rabbitmq");
var testsBuffer = fs.readFileSync(testfileLocation).toString();
var tests = JSON.parse(testsBuffer);
var errorCount = 0;
var i = 0;
function executeTest() {
    "use strict";
    var currentTest = tests[i];
    var test = new ShowcaseTest(currentTest, amqpConnection);
    test.runTest()
        .then(function (success) {
        if (!success) {
            errorCount += 1;
        }
        i += 1;
        if (i < tests.length) {
            executeTest();
        }
        else {
            // todo: display summary
            if (errorCount > 0) {
                console.log(errorCount + " of " + i + " tests generated errors.");
            }
            else {
                console.log(i + " tests completed without errors");
            }
            amqpConnection.close();
        }
    });
}
executeTest();

//# sourceMappingURL=showcase_tests.js.map
