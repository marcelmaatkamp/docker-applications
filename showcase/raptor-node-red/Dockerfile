# INSTALLATION:
#  $ docker build -t greup.pirod.nl:5000/iot/node-red .
#  $ docker push greup.pirod.nl:5000/iot/node-red
#
#  espel$ docker pull greup.pirod.nl:5000/iot/node-red
#  espel$ docker run -p 1880:1880 greup.pirod.nl:5000/iot/node-red

# FROM collinestes/docker-node-oracle:latest
FROM collinestes/docker-node-oracle:4.4.0


# Install Oracle instantclient
# FROM node:argon
#
# RUN apt-get update \
#   && apt-get install -y libaio1 \
#   && apt-get install -y unzip \
#   && mkdir /opt/oracle
# ADD ./oracle .
#
# RUN unzip instantclient-basic-linux.x64-12.1.0.2.0.zip -d /opt/oracle \
#   && unzip instantclient-sdk-linux.x64-12.1.0.2.0.zip -d /opt/oracle  \
#   && mv /opt/oracle/instantclient_12_1 /opt/oracle/instantclient \
#   && ln -s /opt/oracle/instantclient/libclntsh.so.12.1 /opt/oracle/instantclient/libclntsh.so \
#   && ln -s /opt/oracle/instantclient/libocci.so.12.1 /opt/oracle/instantclient/libocci.so
#
# ENV LD_LIBRARY_PATH="/opt/oracle/instantclient"
# ENV OCI_HOME="/opt/oracle/instantclient"
# ENV OCI_LIB_DIR="/opt/oracle/instantclient"
# ENV OCI_INCLUDE_DIR="/opt/oracle/instantclient/sdk/include"
#
# RUN echo '/opt/oracle/instantclient/' | tee -a /etc/ld.so.conf.d/oracle_instant_client.conf && ldconfig

# test if oracledb is installed without errors
# RUN npm install oracledb

# install node-red
COPY Dockerfile package.json server.js /node-red/
WORKDIR /node-red

# first install oracledb latest version (should not be neccessary, but oracledb does not install correctly without this line)
RUN npm install oracledb
# install node-red and extra's
RUN npm install

# expose port
EXPOSE 1880

VOLUME ["/node-red/node_modules","/node-red","/node-red-data"]

# Set the default command to execute
# when creating a new container
CMD ["node", "server"]
