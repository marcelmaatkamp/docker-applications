FROM alpine
RUN apk update 
RUN apk add make automake autoconf gcc libtool curl libevent-dev libssl1.0 musl musl-dev libgcc openssl openssl-dev openssh
RUN mkdir /projects
WORKDIR /projects
RUN wget http://www.cipherdyne.org/psad/download/psad-2.4.3.tar.gz
RUN tar zxf psad-2.4.3.tar.gz
WORKDIR psad-2.4.3
RUN make
MAINTAINER "Marcel Maatkamp <m.maatkamp@gmail.com>"
