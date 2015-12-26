Run the following applications with just one docker-compose command with the help of the docker images from
[danielguerra/bro-debian-elasticsearch](https://hub.docker.com/r/danielguerra/bro-debian-elasticsearch/):

|  Bro IDS   |  Elasticsearch + Kibana | RabbitMQ   | 
| :--- | :--- | :--- | 
| ![Bro IDS](https://www.bro.org/images/bro-eyes.png) | ![Elasticsearch](https://www.runabove.com/images/apps/elasticsearch-and-kibana.png) | ![RabbitMQ](https://www.rabbitmq.com/img/rabbitmq_logo_strap.png) |
|  2.4.1   |  2.1.1 / 4.3.1  | 3.5.7  | 


## Start all the applications with docker-compose
```
docker-compose up
```
## Bro into Elasticsearch 
Stream data from a live pcap into Bro IDS into Elasticsearch via port 1969
```
sudo tcpdump -i eth0 -s 0 -w /dev/stdout | nc localhost 1969
```
See results at [http://localhost:5601/](http://localhost:5601/), but first set a filter 'bro-*' on field 'ts':
![kibana_bro](images/kibana_bro.png)

## Bro into RabbitMQ
Stream data from a live pcap into Bro IDS into RabbitMQ via port 1970
```
sudo tcpdump -i eth0 -s 0 -w /dev/stdout | nc localhost 1970
```
See results in RabbitMQ [http://localhost:15672/](http://localhost:15672/)
![rabbitmq_queue](images/rabbitmq_queue.png)
![rabbbitmq_message](images/rabbitmq_message.png)
