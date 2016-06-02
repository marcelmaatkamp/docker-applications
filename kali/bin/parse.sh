#!/bin/bash

# wlan.sa -e wlan.sa_resolved -e wlan_mgt.ssid -e radiotap.dbm_antsignal

while read line
do
 wlan_sa=$(echo $line | cut -d, -f1)
 wlan_sa_resolved=$(echo $line | cut -d, -f2)
 wlan_mgt_ssid=$(echo $line | cut -d, -f3)
 radiotap_dbm_antsignal=$(echo $line | cut -d, -f4)

 routing_key=$(echo $wlan_sa | sed -e 's/:/./g')
  
 date_epoch=$(date +%s)

 json="{ "
 json+=" \"wifi\" : {" 
 json+=" \"date_epoch\": $date_epoch," 
 json+=" \"location\" : {"
 json+="   \"name\" : \"$LOCATION_NAME\", "
 json+="   \"lon\" : $LOCATION_LAT, "
 json+="   \"lat\" : $LOCATION_LON "
 json+="  },"
 json+=" \"probe_request\": { "
 json+="   \"wlan.sa\" : \"$wlan_sa\", "
 json+="   \"wlan.sa_resolved\" : \"$wlan_sa_resolved\", "
 json+="   \"wlan_mgt.ssid\" : \"$wlan_mgt_ssid\", "
 json+="   \"radiotap.dbm_antsignal\" : $radiotap_dbm_antsignal "
 json+="  }"
 json+=" }"
 json+="}"  
 echo "$json" | amqp-publish -u "amqp://$AMQP_USERNAME:$AMQP_PASSWORD@$AMQP_HOSTNAME" -r "$routing_key" -e wifi.probes -l
done < "${1:-/dev/stdin}"
