# Dockerfile to build the backend in a docker container with an external IDE (in this case visual studio code)
#
# execute the following commands to build this docker image:
#  $ docker build -t backend .
#
# to start the development process with docker
#  $ docker run -d --hostname rabbitmq --name rabbitmq rabbitmq:3
#  $
#  $ docker run --name backend -d -v <absolute_local_showcase_backend_path>:/src --link rabbitmq amqp-ts
# or to start development with docker-compose (does the hard work for you)
#  $ docker-compose up -d
#
# to build execute the following command:
#  $ docker exec backend gulp
#
# to automatically build from within Visual Studio Code with CTRL-SHIFT-B
# and to debug the tests and the main program
# copy the contents of the folder .vscode.example to .vscode
# this defines a special build environment that supports building and debugging from within the docker container

FROM node:latest

WORKDIR /src

# install global modules needed
RUN npm install -g gulp

# copy and install local development libraries
COPY package.json /src/
RUN npm install

# default node debug port
EXPOSE 5858

# needed to keep the docker version of the libraries separate from the local version
VOLUME ["/src", "/src/node_modules"]

#initialize the docker development or production environment
CMD ["npm", "start"]
