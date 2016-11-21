FROM node

RUN git clone https://github.com/googlecreativelab/aiexperiments-ai-duet.git 
WORKDIR aiexperiments-ai-duet/static \
 && npm install webpack -g \
 && npm install \
 && webpack -p

WORKDIR ../server
RUN apt-get update && apt-get install -y python-pip python-dev
RUN pip2 install -r requirements.txt

EXPOSE 8080

CMD ["python", "server.py"]
