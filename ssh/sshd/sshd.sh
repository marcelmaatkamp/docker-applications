#!/bin/sh

mkdir -p /var/run/sshd

# prepare config file for key based auth
sed -i "s/UsePrivilegeSeparation.*/UsePrivilegeSeparation no/g" /etc/ssh/sshd_config
sed -i "s/UsePAM.*/UsePAM no/g" /etc/ssh/sshd_config
sed -i "s/PermitRootLogin.*/PermitRootLogin yes/g" /etc/ssh/sshd_config
sed -i "s/#AuthorizedKeysFile/AuthorizedKeysFile/g" /etc/ssh/sshd_config

# start ssh daemon
# /usr/sbin/sshd -D
ssh-keygen -A 
/usr/sbin/sshd -D
