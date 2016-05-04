FROM isuper/java-oracle:jdk_8

RUN apt-get update &&\
    apt-get install -y\ 
      subversion telnet wget axel vim curl

RUN curl -sSL https://get.docker.com | sh
RUN alias docker="docker -H=tcp://docker:2375"
# RUN mkdir -p /etc/systemd/system/docker.service.d/ &&\
#    cat /lib/systemd/system/docker.service | sed -e 's/\/usr\/bin\/docker/\/usr\/bin\/docker -d $DOCKER_OPTS/g' > /etc/systemd/system/docker.service.d/docker.conf

RUN useradd -ms /bin/bash java
RUN usermod -aG docker java
WORKDIR /data
RUN chown -R java /data

USER java

ENV DOCKER_HOST=tcp://docker:2375
RUN alias docker="docker -H=tcp://docker:2375"

