FROM haproxy:1.6.2

RUN mkdir -p /var/lib/haproxy && \
    groupadd -r haproxy -g 433 && \
    useradd -u 431 -r -g haproxy -d /var/lib/haproxy -s /sbin/nologin -c "Docker image user" haproxy && \
    chown -R haproxy:haproxy /var/lib/haproxy

COPY haproxy.cfg /usr/local/etc/haproxy/haproxy.cfg
CMD ["haproxy", "-db",  "-f", "/usr/local/etc/haproxy/haproxy.cfg"]
