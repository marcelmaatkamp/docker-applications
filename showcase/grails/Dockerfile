FROM java:8
MAINTAINER Marcel Maatkamp <m.maatkamp@gmail.com>

RUN apt-get update && apt-get install -y vim tmux
RUN mkdir /app
WORKDIR /app

RUN rm /bin/sh && ln -s /bin/bash /bin/sh
RUN curl -s get.sdkman.io | bash 
RUN bash -c "source $HOME/.sdkman/bin/sdkman-init.sh && sdk install grails &&\
    wget https://codeload.github.com/grails/grails-profile-repository/zip/master &&\
    unzip master &&\
    mv grails-profile-repository-master/profiles/ /root/.sdkman/candidates/grails/current &&\
    rm -rf master &&\
    rm -rf grails-profile-repository-master"

ENTRYPOINT ["/root/.sdkman/candidates/grails/current/bin/grails"]
