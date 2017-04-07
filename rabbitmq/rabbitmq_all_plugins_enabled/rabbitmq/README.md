# RabbitMQ with all plugins enabled

Latest rabbitmq with the following plugins enabled:
  *  rabbitmq_federation 
  *  rabbitmq_federation_management 
  *  rabbitmq_shovel 
  *  rabbitmq_shovel_management 
  *  rabbitmq_mqtt 
  *  rabbitmq_auth_backend_ldap
  *  rabbitmq_management

(Should you also want RabbitMQ with AMQPS and MQTTS with automatic certificate renewal from LetsEncrypt please take a look at https://hub.docker.com/r/marcelmaatkamp/rabbitmq-amqps-mqtts which is an extension of this docker container).

# Usage

Usage in docker-compose:
 ```
 version: '2'
 services:
  rabbitmq:
   image: marcelmaatkamp/rabbitmq_all_plugins_enabled
   restart: always
   hostname: rabbitmq
   ports:
    - 15672:15672
    - 5672:5672
   environment:
    RABBITMQ_NODENAME: rabbitmq@rabbitmq
   volumes:
    - "rabbitmq:/var/lib/rabbitmq/mnesia"
 volumes:
  rabbitmq:
 ```
 
 # Note 
 
 Do not forget to change password for user guest!
