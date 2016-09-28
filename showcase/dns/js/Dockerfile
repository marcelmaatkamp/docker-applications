FROM node:4
MAINTAINER m.maatkamp@gmail.com version: 0.1

RUN npm install native-dns amqp-ts

EXPOSE 53/udp

ADD js/server.js .
CMD ["node", "server.js"]
