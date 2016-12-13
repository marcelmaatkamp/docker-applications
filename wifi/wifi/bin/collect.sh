#!/bin/bash
device=$LOCATION_DEVICE
iwconfig $device mode monitor
./collect_all_packets.sh
