#!/bin/bash
openssl req -nodes -newkey rsa:2048 -keyout myserver.key -out server.csr

