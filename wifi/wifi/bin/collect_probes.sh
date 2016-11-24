#!/bin/bash
device=$LOCATION_DEVICE
iwconfig $device mode monitor
tshark -l -i $device -T fields -e wlan.sa -e wlan.sa_resolved -e wlan_mgt.ssid -e radiotap.dbm_antsignal -E separator=, -I subtype probereq
