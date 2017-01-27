# amqp-2-ftp
Copies the contents of a RabbitMQ queue into an ftp server. This project defines both the amqp-client and the ftp-server. See docker-compose.yml

## Usage

## Start pureftpd
Start pureftpd and create a user 'kev' with a home directory '/home/ftpuser/kev':
```
$ docker-compose up -d pureftpd
$ docker-compose exec pureftpd bash -c 'mkdir -p /home/ftpuser/kev && chown -R ftpuser:ftpgroup /home/ftpuser/kev'
$ docker-compose exec pureftpd pure-pw useradd kev -u ftpuser -d /home/ftpuser/kev -t 1024 -T 1024 -y 1 -m
$ docker-compose exec pureftpd pure-pw mkdb
```

### Start amqp-tools
After pureftpd has been installed properly run the client:
```
$ docker-compose up amqp-tools
```

### Manual upload a file 
```
$ curl -T consume.sh ftp://pureftpd/ --user kev:ftpuser -vvvv
```
