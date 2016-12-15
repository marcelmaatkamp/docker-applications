# Wifi into RabbitMQ
Log from Wifi all management frames the metadata in RabbitMQ. 

## mointor mode
First set device in monitor mode:
```
$ sudo ifconfig wlan0 down
$ sudo iwconfig wlan0 mode monitor
$ sudo fconfig wlan0 up
$ sudo airmon-ng start wlan0
```

## collection
Device mon0 is now available, edit env.env accordingly and start collector:
```
$ docker-compose up -d --build
$ docker-compose exec ssh sh -c "echo `cat ~/.ssh/id_rsa_imac.pub` > ~/.ssh/authorized_keys"
$ ssh -D 3128 -p 2222 root@server
```

Set a SOCKS5 proxy to localhost:3128 and goto http://rabbitmq:15672/
