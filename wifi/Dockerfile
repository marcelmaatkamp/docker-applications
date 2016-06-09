# FROM kalilinux/kali-linux-docker
FROM ubuntu

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get dist-upgrade -yf && apt-get install -y \
    pciutils usbutils unzip telnet wget axel amqp-tools tshark npm nodejs aircrack-ng curl nodejs libpcap-dev

RUN apt-get install -y nodejs-legacy git subversion vim
WORKDIR wifi
# RUN git clone git://github.com/mranney/node_pcap.git
RUN echo
RUN git clone https://github.com/marcelmaatkamp/node_pcap.git
WORKDIR node_pcap
RUN npm install nan socketwatcher 
RUN npm install . -g
RUN node-gyp configure build install
WORKDIR ..

RUN npm install amqp-ts
# ADD bin/start.sh .
# ADD bin/collect.sh . 
# ADD bin/collect_probes.sh .
# ADD bin/collect_all_packets.sh .
# ADD bin/parse.sh .
# ADD bin/probes.js .
# ADD bin/all.js .
# ADD bin/cap.js .

ENV LOCATION_NAME NAME
ENV LOCATION_LAT 0
ENV LOCATION_LON 0

VOLUME bin

CMD ./start.sh
