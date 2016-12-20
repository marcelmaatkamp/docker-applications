#!/usr/bin/env node
const uuidV4 = require('uuid/v4');
const streamifier = require("streamifier");
const JSFtp = require("jsftp");

var ftp_hostname = process.env.FTP_HOSTNAME;
var ftp_username = process.env.FTP_USERNAME;
var ftp_password = process.env.FTP_PASSWORD;
var ftp = new JSFtp({ host: ftp_hostname }); 
ftp.on('error', function(err) {
 console.error(err)
});
var amqp = require("amqp-ts");
var amqp_hostname = (process.env.AMQP_HOSTNAME != undefined ? process.env.AMQP_HOSTNAME : "rabbitmq");
var amqp_username = (process.env.AMQP_USERNAME != undefined ? process.env.AMQP_USERNAME : "guest");
var amqp_password = (process.env.AMQP_PASSWORD != undefined ? process.env.AMQP_PASSWORD : "guest");
var amqp_queue = (process.env.AMQP_QUEUE != undefined ? process.env.AMQP_QUEUE : "ftp");

var amqp_url = "amqp://"+process.env.AMQP_USERNAME+":"+process.env.AMQP_PASSWORD+"@"+process.env.AMQP_HOSTNAME;
console.log("amqp_url: " + amqp_url);

var connection = new amqp.Connection(amqp_url);
var queue = connection.declareQueue(amqp_queue);

ftp.auth(ftp_username, ftp_password, function (err, data) {
 if (err) return console.error(err);

 console.log("connected..");

 queue.activateConsumer((message) => {
  var buffer = new Buffer(JSON.stringify(message.getContent()));
  ftp.put(new Buffer("kiekeboe!"), "/"+uuidV4(), function(err, socket) {
   if (err) return console.error(err);
  });
 });

});
