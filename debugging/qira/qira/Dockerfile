FROM ubuntu:14.04
RUN apt-get update && \
 apt-get install -y wget xz-utils python-pip python-gevent pkg-config && \
 wget -qO- qira.me/dl | unxz | tar x && cd qira && \
 echo "Y\n" | ./install.sh 

WORKDIR qira
EXPOSE 3002 
ENTRYPOINT ["qira"]
CMD ["/bin/bash"]
