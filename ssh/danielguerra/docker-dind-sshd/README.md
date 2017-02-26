# SSH as a Hidden service

This docker-compose combines:
 * https://hub.docker.com/r/danielguerra/docker-dind-sshd/
 * https://hub.docker.com/r/goldy/tor-hidden-service/

# Installation

## Enable overlayfs as docker storage 
```
$ sudo mkdir /etc/systemd/system/docker.service.d
$ sudo echo "[Service]
ExecStart=/usr/bin/docker daemon -H fd:// --storage-driver=overlay" > /etc/systemd/system/docker.service.d/docker.conf
$ sudo systemctl daemon-reload
$ sudo systemctl restart docker.service
$ docker info | grep 'Storage Driver'
```
# Start the services

## start the ssh-server and hidden-server
```
$ docker-compose up
```

`tor_1  | Entrypoint INFO     ssh: xnjxo3uczacii3yr.onion:4848`

## Upload the public ssh key
```
$ docker-compose run ssh sh -c "echo `cat ~/.ssh/id_rsa.pub` > ~/.ssh/authorized_keys"
```

## Login
```
$ torsocks ssh -p 4848 root@xnjxo3uczacii3yr.onion
```
