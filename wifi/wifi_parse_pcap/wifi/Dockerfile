FROM jfloff/alpine-python
RUN pip install pcap-parser
RUN apk update && apk add tcpdump

CMD [ "tcpdump","-w-", "tcp port 80", "|", "parse_pcap" ]
