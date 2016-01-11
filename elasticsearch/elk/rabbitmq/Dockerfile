FROM rabbitmq:management

RUN rabbitmq-plugins enable --offline \
    rabbitmq_federation \
    rabbitmq_federation_management \
    rabbitmq_shovel \
    rabbitmq_shovel_management \
    rabbitmq_mqtt \
    rabbitmq_auth_backend_ldap \
    rabbitmq_management

ADD rabbitmq.config /etc/rabbitmq/rabbitmq.config
ADD enabled_plugins /etc/rabbitmq/enabled_plugins

COPY rabbitmq.config /etc/rabbitmq/rabbitmq.config"
EXPOSE 1883 5672 15672 25672

# rabbitmqadmin
RUN apt-get update && apt-get install -y wget python
RUN wget https://raw.githubusercontent.com/rabbitmq/rabbitmq-management/rabbitmq_v3_6_0/bin/rabbitmqadmin -O /usr/local/bin/rabbitmqadmin
RUN chmod a+rx /usr/local/bin/rabbitmqadmin
CMD rabbitmq-server -detached && \
    sleep 5 && \
    echo "declaring exchanges and queues .. " && \
    /usr/local/bin/rabbitmqadmin declare exchange name=logging type=fanout && \
    /usr/local/bin/rabbitmqadmin declare queue name=logging.kibana durable=false && \
    /usr/local/bin/rabbitmqadmin declare binding source="logging" destination_type="queue" destination="logging.kibana" && \
    tail -f /var/log/dmesg
