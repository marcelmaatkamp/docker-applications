ssh front for docker:dind

### Dependencies

* [![2.0.0](https://badge.imagelayers.io/docker.svg)](https://imagelayers.io/?images=docker:1.9 '1.9') docker:1.9

### Image Size

* [![Latest](https://badge.imagelayers.io/danielguerra/docker-sshd.svg)](https://imagelayers.io/?images=danielguerra/docker-sshd:latest 'latest') danielguerra/docker-sshd

### Usage

start docker dind server
```bash
docker run --privileged --name shared-docker -d docker:dind
```
create an empty ssh volume
```bash
$ docker create -v /root/.ssh --name ssh-container danielguerra/ssh-container /bin/true
```
create your own keys on your own machine
```bash
$ docker run --volumes-from ssh-container danielguerra/docker-sshd ssh-keygen -q
```
add your pub key to authorized_keys file
```bash
$ docker run --volumes-from ssh-container danielguerra/docker-sshd cp /root/.ssh/id_rsa.pub /root/.ssh/authorized_keys
```
create a copy in your directory (pwd)
```bash
$ docker run --volumes-from ssh-container -v $(pwd):/backup danielguerra/docker-sshd cp -R /root/.ssh/* /backup
```

start docker ssh front
```bash
docker run -d -p 2222:22 --net=container:shared-docker --name ssh-docker --volumes-from ssh-container --link shared-docker:docker danielguerra/docker-sshd
```

### Routing to containers started in ssh-docker
In order to forward or connect to the started containers in ssh-docker you need to add a route to the shared-docker of the main docker host to the shared-dockerhost.
In ssh-docker
```bash
docker inspect my-container-name
```
Check the container-ip

In the main docker , in which the shared and ssh docker is running do
```bash
route add -net container-ip-net netmask 255.255.255.0 gw shared-docker-ip
```
