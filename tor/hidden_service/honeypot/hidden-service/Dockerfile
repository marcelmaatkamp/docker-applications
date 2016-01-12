FROM marcelmaatkamp/tor-hidden-service
RUN apt-get install -y tcpdump netcat
COPY contrib/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY contrib/torrc /etc/tor/torrc
COPY contrib/tap.sh /usr/local/bin/tap.sh
