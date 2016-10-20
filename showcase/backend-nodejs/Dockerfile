# Dockerfile to build test in a docker container with an external IDE (in this case visual studio code)
#
# TODO: gecopieerde code aanpassen voor dit onderdeel
#
# execute the following commands to build this docker image:
#  $ docker build -t test .
#
# to start the development process with docker
#  $ docker run -d --hostname rabbitmq --name rabbitmq rabbitmq:3
#  $
#  $ docker run --name test -d -v <absolute_local_showcase_test_path>:/src --link rabbitmq amqp-ts
# or to start development with docker-compose (does the hard work for you)
#  $ docker-compose up -d
#
# to build execute the following command:
#  $ docker exec test gulp
#
# to automatically build from within Visual Studio Code with CTRL-SHIFT-B
# create a tasks.json file in the .vscode folder with the following content:
# {
#     // See https://go.microsoft.com/fwlink/?LinkId=733558
#     // for the documentation about the tasks.json format
#     "version": "0.1.0",
#     "command": "docker",
#     "isShellCommand": true,
#     "args": ["exec", "dev-amqp-ts", "gulp"],
#     "showOutput": "always"
# }
#
# to debug execute the following line and run the 'Attach' Visual Studio Code Debugger:
#  $ docker exec test node --debug-brk --nolazy /src/showcase_tests.js
# todo: create better integration
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
