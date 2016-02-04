FROM marcelmaatkamp/alpine-build-base
RUN apk add --update git
RUN git clone https://github.com/DonnchaC/tor-hsdir-research.git
WORKDIR tor-hsdir-research
RUN ./autogen.sh && ./configure --disable-asciidoc && make && make install && make dist-gzip
EXPOSE 9001 9002
CMD tor -f /etc/tor/torrc
