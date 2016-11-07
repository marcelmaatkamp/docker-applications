FROM danielguerra/alpine-xfce4-xrdp

USER root
RUN apk --update --no-cache add git
RUN git clone https://github.com/deltaxflux/fluxion.git
WORKDIR fluxion

CMD ./fluxion
