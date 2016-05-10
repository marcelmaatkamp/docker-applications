#!/bin/bash
amqp-consume -u amqp://localhost:5672 -q rtl_433 ./bin/plain.sh >> data
