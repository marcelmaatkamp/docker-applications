#!/usr/bin/env node
var pcap=require('../node_pcap/pcap');
var amqp = require("amqp-ts");

var amqp_hostname = (process.env.AMQP_HOSTNAME != undefined ? process.env.AMQP_HOSTNAME : "rabbitmq");
var amqp_username = (process.env.AMQP_USERNAME != undefined ? process.env.AMQP_USERNAME : "guest");
var amqp_password = (process.env.AMQP_PASSWORD != undefined ? process.env.AMQP_PASSWORD : "guest");
var amqp_exchange = (process.env.AMQP_EXCHANGE != undefined ? process.env.AMQP_USERNAME : "wifi");

var amqp_url = "amqp://"+process.env.AMQP_USERNAME+":"+process.env.AMQP_PASSWORD+"@"+process.env.AMQP_HOSTNAME;
console.log("amqp_url: " + amqp_url);

var wifi = {
  location: { 
    name: process.env.LOCATION_NAME,
    lat: process.env.LOCATION_LAT,
    lon: process.env.LOCATION_LON
  }
}

var connection = new amqp.Connection(amqp_url);
var exchange = connection.declareExchange(process.env.AMQP_EXCHANGE, "topic", {durable: false} );
connection.completeConfiguration().then(() => {
  pcap.createSession(process.env.LOCATION_DEVICE, '').on('packet', function (raw_packet) {
   wifi.packet = pcap.decode.packet(raw_packet);
   var addr = (wifi.packet.payload.ieee802_11Frame.shost.addr.map(function (x) {return (x<15 ? "0" + x.toString(16).toUpperCase() : x.toString(16).toUpperCase() );})).join(".");
   if(!(wifi.packet.payload.ieee802_11Frame.type == 0 && wifi.packet.payload.ieee802_11Frame.subType == 8)) { 
     var msg = new amqp.Message(JSON.stringify(wifi));
     exchange.send(msg,addr);
     console.log(addr + " " + JSON.stringify(wifi));
   } else { 
     // console.log(addr);
   }
 });
});
