FROM marcelmaatkamp/alpine-build-base
RUN apk add --update git
RUN git clone https://github.com/marcelmaatkamp/tor.git
WORKDIR tor

RUN ./autogen.sh && ./configure --disable-asciidoc && make && make install && make dist-gzip
EXPOSE 9001 9002
CMD tor -f /etc/tor/torrc
