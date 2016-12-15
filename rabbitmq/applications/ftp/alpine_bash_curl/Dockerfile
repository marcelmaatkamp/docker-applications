FROM alpine:edge
RUN mkdir -p /usr/local/sbin \
    && echo http://dl-cdn.alpinelinux.org/alpine/edge/main | tee /etc/apk/repositories \
    && echo @testing http://dl-cdn.alpinelinux.org/alpine/edge/testing | tee -a /etc/apk/repositories \
    && echo @community http://dl-cdn.alpinelinux.org/alpine/edge/community | tee -a /etc/apk/repositories \
    && apk add --update bash openssl curl \
    && apk add --no-cache -X http://dl-4.alpinelinux.org/alpine/edge/testing rabbitmq-c-utils \
    && rm -rf /var/cache/apk/*
COPY bin/consume.sh consume.sh
COPY bin/consumer.sh consumer.sh
CMD ["./consumer.sh"]
