#!/bin/sh

mkdir -p /var/run/sshd

ssh-keygen -A 
/usr/sbin/sshd -D 
