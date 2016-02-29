FROM ubuntu:15.10
RUN apt-get update && apt-get -y upgrade && apt-get -y install software-properties-common && add-apt-repository -y ppa:ethereum/ethereum && add-apt-repository -y ppa:ethereum/ethereum-dev && apt-get -y update && apt-get -y install ethminer
ENTRYPOINT ["ethminer"]
