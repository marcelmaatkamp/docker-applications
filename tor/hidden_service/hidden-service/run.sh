#!/bin/bash
docker run --name cadvisor_hidden --link cadvisor_cadvisor_1 marcelmaatkamp/tor-hidden-service
