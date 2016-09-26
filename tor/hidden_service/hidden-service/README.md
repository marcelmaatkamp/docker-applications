# docker-tor-hidden-service

This is the revised version from [cmehay/docker-tor-hidden-service](https://github.com/cmehay/docker-tor-hidden-service) which has the following extra features
 * survives a docker restart of the service instead of adding the same service over and over in the config file
 * and saves the keys in the directory with the name of the onion

## Wordpress example
![wordpress as hidden service](https://github.com/marcelmaatkamp/docker-applications/raw/master/tor/hidden_service/hidden-service/assets/tor-hidden-service-wordpress.gif)
```
 $ docker-compose -f docker-compose-wordpress.yml up
```

## RabbitMQ example
![rabbitmq as hidden service](https://github.com/marcelmaatkamp/docker-applications/raw/master/tor/hidden_service/hidden-service/assets/tor-hidden-service-rabbitmq.gif)
```
 $ docker-compose -f docker-compose-rabbitmq.yml up
```

## USAGE
Link a docker container and create a tor hidden service from it
```
# run a container with an network application
$ docker run -d \
    --name hello_world \
    tutum/hello_world

# and just link it to this container
$ docker run -ti \
    --link hello_world \
    marcelmaatkamp/tor-hidden-service
```
The .onion url is displayed to stdout at startup.

To keep onion keys, just mount volume `/var/lib/tor/hidden_service/`
```
$ docker run -ti \
    --link some_service \
    --volume /path/to/keys:/var/lib/tor/hidden_service/ \
    marcelmaatkamp/tor-hidden-service
```

Look at the `docker-compose.yml` file to see how to use it.
