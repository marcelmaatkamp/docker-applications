FROM mhart/alpine-node

WORKDIR js
COPY js/package.json package.json
COPY js/amqp_ftp.js amqp_ftp.js
RUN npm install 

CMD ["node", "amqp_ftp.js"]
