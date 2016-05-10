#!/bin/bash
read line

lat=52.123895
lon=5.422330
echo "{ sensor:rtl_433, lon:$lon, lat:$lat, data:$line }"
