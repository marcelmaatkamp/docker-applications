# Pre-installation

```
sudo mkdir /etc/systemd/system/docker.service.d
sudo echo "[Service]
> ExecStart=
> ExecStart=/usr/bin/docker daemon -H fd:// --storage-driver=overlay" > /etc/systemd/system/docker.service.d/docker.conf
sudo systemctl daemon-reload
sudo systemctl restart docker.service
docker info | grep 'Storage Driver'
```

# start
```
docker-compose up
```
