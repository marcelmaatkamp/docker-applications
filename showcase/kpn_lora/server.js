"use strict";

const express = require("express");
const PORT = 8080;

var amqp = require("amqp-ts");

var rabbitmq_url = process.env.AMQP_URL || "amqp://rabbitmq"
var connection = new amqp.Connection(rabbitmq_url);
var exchange = connection.declareExchange("lora",  'fanout', {durable: true});

connection.completeConfiguration().then(() => {
  console.log("succesfull connected at " +rabbitmq_url)
  const app = express();

  var bodyParser = require('body-parser');
  app.use(bodyParser.json()); 
  app.use(bodyParser.urlencoded({ extended: true })); 

  app.get("/", function (req, res) {
    res.send("KPN Receiver, post to /lora\n");
  });

  app.post("/lora", function (req, res) {
    var message = JSON.stringify(req.body)
    console.log("lora: " + message + "\n");
    res.end("thanks!");
    exchange.send(new amqp.Message(message));
  });

  app.listen(PORT);
  console.log("Running on http://localhost:" + PORT);
});
