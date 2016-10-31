FROM ubuntu

RUN apt-get update && apt-get install -y \
  bash git curl unzip wget axel telnet 

# create user
ENV user=fluter
RUN useradd -ms /bin/bash ${user}
USER ${user}
WORKDIR /home/${user}

# extract source 
RUN git clone https://github.com/flutter/flutter.git -b alpha
ENV PATH=$PWD/flutter/bin:$PATH

RUN flutter doctor
