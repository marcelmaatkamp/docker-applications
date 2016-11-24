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
docker-compose up --build
```
