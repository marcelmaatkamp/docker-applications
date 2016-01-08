FROM ubuntu

RUN \
  apt-get update && \
  DEBIAN_FRONTEND=noninteractive apt-get install -y lxde-core lxterminal tightvncserver && \
  rm -rf /var/lib/apt/lists/*

ENV PASSWORD vncpassword
RUN echo "password\npassword\nn\n" | vncpasswd 
WORKDIR /data
CMD ["bash"]
EXPOSE 5901
