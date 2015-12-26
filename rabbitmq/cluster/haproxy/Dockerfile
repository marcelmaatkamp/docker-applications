FROM haproxy:1.6.2

RUN mkdir -p /var/lib/haproxy && mkdir -p /var/log/haproxy && \
    groupadd -r haproxy  && \
    useradd -r -g haproxy -s /sbin/nologin -c "Docker image user" haproxy && \
    chown -R haproxy:haproxy /var/lib/haproxy && \
    chown -R haproxy:haproxy /var/log/haproxy

COPY haproxy.cfg /usr/local/etc/haproxy/haproxy.cfg

# USER haproxy
WORKDIR /var/lib/haproxy

CMD ["haproxy", "-db",  "-f", "/usr/local/etc/haproxy/haproxy.cfg"]
