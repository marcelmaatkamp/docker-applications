FROM ubuntu:14.04
RUN apt-get update 
RUN apt-get install -y wget xz-utils python-pip 
RUN wget -qO- qira.me/dl | unxz | tar x && cd qira 
WORKDIR qira
RUN echo "Y\n" | ./install.sh 
