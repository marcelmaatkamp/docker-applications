This is the image to compile the binary package tor version '0.2.7.6' for alpine-linux distribution to make the smallest docker tor image possible
```
$ docker run --name tor -p 9001:9001 -p 9050:9050 marcelmaatkamp/alpine-build-tor:latest
```
