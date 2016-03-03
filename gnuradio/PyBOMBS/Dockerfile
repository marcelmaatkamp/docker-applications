FROM ubuntu:14.04

# python && pip

RUN apt-get update && apt-get install -y \
  python-pip python-dev build-essential git &&\
  pip install --upgrade pip &&\
  pip install --upgrade virtualenv

# GNURadio

WORKDIR /projects
RUN \ 
  # pip install PyBOMBS &&\
  pip install git+https://github.com/gnuradio/pybombs.git &&\
  pybombs recipes add gr-recipes git+https://github.com/gnuradio/gr-recipes.git &&\
  pybombs recipes add gr-etcetera git+https://github.com/gnuradio/gr-etcetera.git &&\
  pybombs prefix init gnuradio -a myprefix &&\
  pybombs install gnuradio gr-osmosdr
