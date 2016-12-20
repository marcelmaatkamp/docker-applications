#!/usr/bin/env node
const uuidV4 = require('uuid/v4');
const streamifier = require("streamifier");
const Client = require('ftp');

var ftp_hostname = process.env.FTP_HOSTNAME;
var ftp_username = process.env.FTP_USERNAME;
var ftp_password = process.env.FTP_PASSWORD;

var amqp = require("amqp-ts");
var amqp_hostname = (process.env.AMQP_HOSTNAME != undefined ? process.env.AMQP_HOSTNAME : "rabbitmq");
var amqp_username = (process.env.AMQP_USERNAME != undefined ? process.env.AMQP_USERNAME : "guest");
var amqp_password = (process.env.AMQP_PASSWORD != undefined ? process.env.AMQP_PASSWORD : "guest");
var amqp_queue = (process.env.AMQP_QUEUE != undefined ? process.env.AMQP_QUEUE : "ftp");

var amqp_url = "amqp://"+process.env.AMQP_USERNAME+":"+process.env.AMQP_PASSWORD+"@"+process.env.AMQP_HOSTNAME;
console.log("amqp_url: " + amqp_url);
var ftp_connect = { host: ftp_hostname, user: ftp_username, password: ftp_password}
console.log("ftp: " + JSON.stringify(ftp_connect));

var connection = new amqp.Connection(amqp_url);
var queue = connection.declareQueue(amqp_queue);

var i = 0;

var c = new Client();
c.on('ready', function() {
 queue.activateConsumer((message) => {
  var filename = "/"+uuidV4()+".json";
  var content = new Buffer(JSON.stringify(message.getContent()));

  c.put(content, filename, function(err) {
   if (err) throw err;
    console.log("["+(i++)+"] filename("+filename+"): " + content.length + " bytes..");
    c.end();
  });

 });
});

c.connect(ftp_connect);
