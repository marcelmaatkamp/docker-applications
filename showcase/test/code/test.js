"use strict";
var amqp = require("amqp-ts");
var connection = new amqp.Connection("amqp://rabbitmq");
connection.declareExchange("test");
connection.completeConfiguration().then(function () {
    connection.close();
});

//# sourceMappingURL=test.js.map
