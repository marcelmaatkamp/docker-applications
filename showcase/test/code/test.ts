import * as amqp from "amqp-ts";

var connection = new amqp.Connection("amqp://rabbitmq");
connection.declareExchange("test");
connection.completeConfiguration().then(() => {
    connection.close();
});
