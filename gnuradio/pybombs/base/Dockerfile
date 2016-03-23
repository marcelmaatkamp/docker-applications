FROM ubuntu:14.04

# python && pip

RUN apt-get update && apt-get install -y \
  python-pip python-dev build-essential git &&\
  pip install --upgrade pip &&\
  pip install --upgrade virtualenv

# GNURadio

WORKDIR /projects
RUN apt-get install git python-pip
RUN pip install PyBOMBS
RUN pybombs prefix init /usr/local -a default_prx
RUN pybombs config default_prefix default_prx
RUN pybombs recipes add gr-recipes git+https://github.com/gnuradio/gr-recipes.git
RUN pybombs recipes add gr-etcetera git+https://github.com/gnuradio/gr-etcetera.git
