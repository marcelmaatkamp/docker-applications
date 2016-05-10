#!/bin/bash
echo "{"time" : "2016-05-03 12:35:22", "model" : "AlectoV1 Temperature Sensor", "id" : 45, "channel" : 1, "battery" : "LOW", "temperature_C" : 17.200, "humidity" : 60}" | amqp-publish -u amqp://localhost:5672 -e faces && amqp-consume -u amqp://localhost:5672 -q faces ./bin/plain.sh
