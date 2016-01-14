#!/bin/bash
docker run -d --restart=always -v $PWD/volumes/tcpdump:/data --net=container:honeypot_honey_1 crccheck/tcpdump -i eth0 -w /data/honeypot_`date +%Y_%m_%d_%H_%M_%S_%N`.pcap
