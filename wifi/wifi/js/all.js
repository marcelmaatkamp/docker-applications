#!/usr/bin/env node
var pcap=require('../node_pcap/pcap');
var amqp = require("amqp-ts");

var wifi = {
  location: { 
    name: process.env.LOCATION_NAME,
    lat: process.env.LOCATION_LAT,
    lon: process.env.LOCATION_LON
  }
}

var connection = new amqp.Connection("amqp://"+process.env.AMQP_USERNAME+":"+process.env.AMQP_PASSWORD+"@"+process.env.AMQP_HOSTNAME+"");
var exchange = connection.declareExchange("wifi", "topic", {durable: false} );

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
