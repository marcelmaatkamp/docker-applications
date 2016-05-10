FROM marcelmaatkamp/pybombs-gnuradio

MAINTAINER Marcel Maatkamp but inspired by Frederik Granna

RUN pybombs install rtl-sdr && ldconfig

RUN apt-get update && \
    apt-get install -y libfftw3-dev nmap python2.7 vim --no-install-recommends && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /tmp

ENV commit_id 0fd32aabbd56d3d5d11f828b933d85dab62d5680

RUN git clone https://github.com/simonyiszk/csdr.git && \
    cd csdr && \
    git reset --hard $commit_id && \
    make && \
    make install && \
    cd / && \
    rm -rf /tmp/csdr

WORKDIR /opt

ENV commit_id 6b06d13a934a093064f231f37bdafee6a3cc3b1c
ENV branch dev2

RUN git clone https://github.com/simonyiszk/openwebrx.git && \
    cd openwebrx && \
    git checkout $branch && \
    git reset --hard $commit_id

WORKDIR /opt/openwebrx

EXPOSE 8073 8888 4951

CMD openwebrx.py
