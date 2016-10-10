FROM node:4
MAINTAINER m.maatkamp@gmail.com version: 0.1

RUN npm install native-dns amqp-ts console-stamp

EXPOSE 53/udp

WORKDIR /app
ADD js/server.js .

CMD ["node", "server.js"]
