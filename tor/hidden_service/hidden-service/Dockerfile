FROM  debian:jessie
ENV DEBIAN_FRONTEND=noninteractive
RUN   apt-get update && apt-get install -y \
        tor \
        python3 \
        git \
        ca-certificates 

RUN   git clone https://github.com/cmehay/python-docker-tool.git /docker && \
      touch /docker/__init__.py

ADD   assets/docker-entrypoint.sh /docker-entrypoint.sh
ADD   assets/tor_config.py /docker/tor_config.py
RUN   chmod +x /docker-entrypoint.sh 
VOLUME /var/lib/tor/hidden_service/
CMD /docker-entrypoint.sh tor
