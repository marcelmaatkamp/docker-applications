FROM ubuntu

RUN apt-get update && apt-get -y dist-upgrade
RUN apt-get install -y git subversion build-essential

RUN git config --global user.email "m.maatkamp@gmail.com"
RUN git config --global user.name "Marcel Maatkamp"

VOLUME /projects
