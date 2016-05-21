FROM marcelmaatkamp/pybombs-gnuradio-rtlsdr

RUN git clone https://github.com/pothosware/SoapySDR.git
WORKDIR SoapySDR
WORKDIR build
RUN cmake .. -DCMAKE_BUILD_TYPE=Release
RUN make
RUN make install
WORKDIR ../..

RUN git clone https://github.com/pothosware/SoapyRTLSDR.git
WORKDIR SoapyRTLSDR
WORKDIR build
RUN cmake .. -DCMAKE_BUILD_TYPE=Release
RUN make
RUN make install
# SoapySDRUtil --probe
WORKDIR ../..

RUN git clone https://github.com/pothosware/SoapyRemote.git
WORKDIR SoapyRemote
WORKDIR build
RUN cmake ..
RUN make
RUN make install

# RUN git clone https://github.com/pothosware/SoapySDRPlay.git
# WORKDIR SoapySDRPlay
# WORKDIR SoapySDR
# RUN cmake .. -DCMAKE_BUILD_TYPE=Release
# RUN make
# RUN make install
# WORKDIR ../..

RUN ldconfig

EXPOSE 55132
EXPOSE 1900/udp

CMD SoapySDRServer --bind="0.0.0.0:1234"
