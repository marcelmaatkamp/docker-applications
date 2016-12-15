FROM alpine
RUN  apk add --no-cache git bash && \
     git clone https://github.com/basecamp/xip-pdns.git
WORKDIR xip-pdns
EXPOSE 53
VOLUME etc
CMD ["bin/xip-pdns","etc/xip-pdns.conf"]
