FROM docker:1.9
# docker ssh front docker is based on alpine
MAINTAINER Daniel Guerra <daniel.guerra69@gmail.com>

# add openssh package and docker-compose
ENV DOCKER_COMPOSE_VERSION 1.5.2

RUN apk --update add py-pip py-yaml openssh &&\
    pip install -U docker-compose==${DOCKER_COMPOSE_VERSION} &&\
    rm -rf `find / -regex '.*\.py[co]' -or -name apk`

# script generates new server key, sets sshd config for keybased auth and starts sshd
ADD sshd.sh /bin/sshd.sh

RUN sed -i "s/UsePrivilegeSeparation.*/UsePrivilegeSeparation no/g" /etc/ssh/sshd_config && \
    echo "PermitRootLogin no" >> /etc/ssh/sshd_config 

ENV ADMIN marcel 
RUN addgroup $ADMIN && \ 
    adduser -S $ADMIN -G $ADMIN -s /bin/sh && \
    passwd -d $ADMIN && \ 
    mkdir -p /home/$ADMIN/.ssh && \
    wget -qO- https://api.github.com/users/marcelmaatkamp/keys | grep "\"key\"\: \"" | sed -e 's/    "key\"\: \"//g' | sed -e 's/\"//g' > /home/$ADMIN/.ssh/authorized_keys  && \ 
    chown -R $ADMIN:$ADMIN /home/$ADMIN/.ssh && \
    chmod 744 /home/$ADMIN/.ssh/authorized_keys

CMD ["/bin/sshd.sh"]
