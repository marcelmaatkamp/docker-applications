#!/bin/bash
tshark -l -i wlan0mon -T fields -e wlan.sa -e wlan.sa_resolved -e wlan_mgt.ssid -e radiotap.dbm_antsignal -E separator=, -I subtype probereq
