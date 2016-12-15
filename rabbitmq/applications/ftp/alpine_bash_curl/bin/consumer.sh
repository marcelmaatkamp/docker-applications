#!/bin/bash
echo "Connecting to amqp://${AMQP_USERNAME}:${AMQP_PASSWORD}@${AMQP_SERVER}/${AMQP_QUEUE}" and pushing to ftp://${FTP_USERNAME}:${FTP_PASSWORD}@${FTP_SERVER}/${FTP_DIRECTORY}
amqp-consume -u amqp://${AMQP_USERNAME}:${AMQP_PASSWORD}@${AMQP_SERVER} -q ${AMQP_QUEUE} ./consume.sh
