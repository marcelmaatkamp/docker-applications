#!/bin/bash
docker run -v $PWD/volumes/tcpdump:/data k0st/snort -r /data/honeypot.pcap
