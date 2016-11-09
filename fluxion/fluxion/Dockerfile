FROM danielguerra/alpine-xfce4-xrdp

ADD packages /packages
RUN cp /packages/.abuild/-58231917.rsa.pub /etc/apk/keys &&\
    apk update &&\
    apk add aircrack-ng curl dhcp hostapd lighttpd nmap python xterm zenity \
    /packages/unmaintained/x86_64/macchanger-1.7.0-r0.apk &&\
    rm -rf /tmp/* /var/cache/apk/*

RUN apk --update --no-cache add git
RUN git clone https://github.com/deltaxflux/fluxion.git

