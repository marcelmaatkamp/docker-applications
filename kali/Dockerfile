FROM kalilinux/kali-linux-docker

RUN apt-get update && apt-get dist-upgrade -yf && apt-get install -y \
    pciutils usbutils unzip telnet wget axel amqp-tools tshark npm nodejs aircrack-ng

ADD bin/start.sh wifi/
ADD bin/collect.sh wifi/
ADD bin/collect_probes.sh wifi/
ADD bin/collect_all_packets.sh wifi/
ADD bin/parse.sh wifi/

WORKDIR wifi 

ENV LOCATION_NAME NAME
ENV LOCATION_LAT 0
ENV LOCATION_LON 0

CMD ./start.sh
