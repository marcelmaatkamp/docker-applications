FROM  debian:jessie
ENV DEBIAN_FRONTEND=noninteractive
RUN   apt-get update && apt-get install -y \
        tor \
        python3 \
        git \
        ca-certificates \ 
        apache2 supervisor

RUN   mkdir -p /var/lock/apache2 /var/run/apache2 /var/run/sshd /var/log/supervisor
RUN   git clone https://github.com/cmehay/python-docker-tool.git /docker && \
      touch /docker/__init__.py

ADD   assets/docker-entrypoint.sh /docker-entrypoint.sh
ADD   assets/tor_config.py /docker/tor_config.py
COPY  assets/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN   chmod +x /docker-entrypoint.sh 

VOLUME /var/lib/tor/hidden_service/
# ENTRYPOINT /docker-entrypoint.sh
EXPOSE 80

# CMD /docker-entrypoint.sh tor
CMD /usr/bin/supervisord
