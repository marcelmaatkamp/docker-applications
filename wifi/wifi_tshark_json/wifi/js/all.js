#!/usr/bin/env node
var pcap=require('../node_pcap/pcap');

pcap.createSession(process.env.LOCATION_DEVICE, '').on('packet', function (raw_packet) {
 var packet = pcap.decode.packet(raw_packet);
 console.log(JSON.stringify(packet));
});
