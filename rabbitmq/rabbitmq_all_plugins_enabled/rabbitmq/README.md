# RabbitMQ with all plugins enabled

Latest rabbitmq with the following enabled plugins:
* rabbitmq_mqtt
* rabbitmq_management
* rabbitmq_federation
* rabbitmq_federation_management
* rabbitmq_shovel
* rabbitmq_shovel_management 

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
