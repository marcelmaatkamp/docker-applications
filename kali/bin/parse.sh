#!/bin/bash

# wlan.sa -e wlan.sa_resolved -e wlan_mgt.ssid -e radiotap.dbm_antsignal

while read line
do
 wlan_sa=$(echo $line | cut -d, -f1)
 wlan_sa_resolved=$(echo $line | cut -d, -f2)
 wlan_mgt_ssid=$(echo $line | cut -d, -f3)
 radiotap_dbm_antsignal=$(echo $line | cut -d, -f4)

 date_epoch=$(date +%s)

 echo -n "{ "
 echo -n " \"wifi\" : {" 
 echo -n " \"date_epoch\": $date_epoch," 
 echo -n " \"location\" : {"
 echo -n "   \"name\" : \"$LOCATION_NAME\", "
 echo -n "   \"lon\" : $LOCATION_LAT, "
 echo -n "   \"lat\" : $LOCATION_LON "
 echo -n "  },"
 echo -n " \"probe_request\": { "
 echo -n "   \"wlan.sa\" : \"$wlan_sa\", "
 echo -n "   \"wlan.sa_resolved\" : \"$wlan_sa_resolved\", "
 echo -n "   \"wlan_mgt.ssid\" : \"$wlan_mgt_ssid\", "
 echo -n "   \"radiotap.dbm_antsignal\" : $radiotap_dbm_antsignal "
 echo -n "  }"
 echo -n " }"
 echo "}"
done < "${1:-/dev/stdin}"
