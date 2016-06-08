var pcap=require('./pcap');
pcap.createSession("mon0", '(type mgt) and (type mgt subtype probe-req )').
        on('packet', function (raw_packet) {
                with(pcap.decode.packet(raw_packet).link.ieee802_11Frame)
                        if (type == 0 && subType == 4)
                                console.log("Probe request",shost, "-> ",bssid);
        }
);
