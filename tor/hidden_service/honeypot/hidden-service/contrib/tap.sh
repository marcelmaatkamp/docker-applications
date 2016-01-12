#!/bin/bash
/usr/sbin/tcpdump -i eth0 -s 0 -w /dev/stdout | nc bro 1969
