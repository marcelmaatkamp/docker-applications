#!/bin/bash
tshark -l -i $LOCATION_DEVICE  -T fields -e wlan.sa -e wlan.sa_resolved -e wlan_mgt.ssid -e wlan.fc.type_subtype -e radiotap.dbm_antsignal -E separator=,  -Y 'wlan.fc.type_subtype ne 24 and wlan.fc.type_subtype ne 25 and wlan.fc.type_subtype ne 27 and wlan.fc.type_subtype ne 28 and wlan.fc.type_subtype ne 29'
