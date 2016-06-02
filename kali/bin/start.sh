#!/bin/bash
./collect.sh 2> /dev/null | ./parse.sh | amqp-publish -u amqp://wifi.probes:wifi.probes@stein.pirod.nl -e wifi.probes -l
