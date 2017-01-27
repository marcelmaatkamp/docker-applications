#!/usr/bin/env node
const uuidV4 = require('uuid/v4');
const streamifier = require("streamifier");
const PromiseFtp = require('promise-ftp');
 
var ftp_hostname = process.env.FTP_HOSTNAME;
var ftp_username = process.env.FTP_USERNAME;
var ftp_password = process.env.FTP_PASSWORD;
var config = {
 host: ftp_hostname,
 port: 21,
 user: ftp_username,
 pass: ftp_password,
 debug: false
};
 
var amqp = require("amqp-ts");
var amqp_hostname = (process.env.AMQP_HOSTNAME != undefined ? process.env.AMQP_HOSTNAME : "rabbitmq");
var amqp_username = (process.env.AMQP_USERNAME != undefined ? process.env.AMQP_USERNAME : "guest");
var amqp_password = (process.env.AMQP_PASSWORD != undefined ? process.env.AMQP_PASSWORD : "guest");
var amqp_queue = (process.env.AMQP_QUEUE != undefined ? process.env.AMQP_QUEUE : "ftp");
 
var amqp_url = "amqp://"+process.env.AMQP_USERNAME+":"+process.env.AMQP_PASSWORD+"@"+process.env.AMQP_HOSTNAME;
console.log("amqp_url: " + amqp_url);
 
var connection = new amqp.Connection(amqp_url);
var queue = connection.declareQueue(amqp_queue, {} );
queue.prefetch(1);

var FTP = require('ftpimp'),
 ftp = FTP.create(config),
 connected = function () {
  console.log('connected to remote FTP server');
  queue.activateConsumer((message) => {
   var filename = "/" + uuidV4();
   console.log("start sending FTP file: " + filename);
   ftp.put(message.content, filename)
   .then(() => {
     console.log("finished sending FTP file: " + filename);
   }, err => {
     console.log("Error sending file " + filename + ": " + err.message);
   })
 }, {noAck: true});
};
