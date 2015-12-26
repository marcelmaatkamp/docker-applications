# Bro + Elastisearch + RabbitMQ

```
 docker-compose up
```

To stream a pcap:
```
sudo tcpdump -i eth0 -s 0 -w /dev/stdout | nc localhost 1969a
```

See results at:
 http://localhost:5601

Set filter:
  bro-*, timestamp = ts 

