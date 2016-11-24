#!/usr/bin/env node
var pcap=require('../node_pcap/pcap');
pcap.createSession(process.env.LOCATION_DEVICE, '').on('packet', function (raw_packet) {
    var packet = pcap.decode.packet(raw_packet);
    console.log("type("+packet.payload.ieee802_11Frame.type+","+packet.payload.ieee802_11Frame.subType+")");
  }
);
