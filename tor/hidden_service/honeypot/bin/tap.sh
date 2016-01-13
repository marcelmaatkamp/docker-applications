#!/bin/bash
docker run -v $PWD/volumes/tcpdump:/data --net=container:honeypot_honey_1 crccheck/tcpdump -i eth0 -w /data/hs.pcap
