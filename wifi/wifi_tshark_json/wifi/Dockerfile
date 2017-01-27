# FROM kalilinux/kali-linux-docker
FROM ubuntu

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get dist-upgrade -yf
RUN apt-get install -y software-properties-common
RUN add-apt-repository -yu ppa:wireshark-dev/stable
RUN apt-get install -y \
    pciutils usbutils unzip telnet wget axel amqp-tools tshark npm nodejs aircrack-ng curl nodejs libpcap-dev
RUN apt-get install -y nodejs-legacy git subversion vim

WORKDIR wifi
RUN npm install -g amqp-ts

CMD [ "nodejs", "js/all.js" ]
