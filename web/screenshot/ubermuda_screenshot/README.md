### Take screenshots of urls
```
$ docker run -d \
    --name tor \
    -p 9050:9050 \
    marcelmaatkamp/alpine-tor:latest
```
```
$ docker run -ti \
   -v $PWD/images:/srv \
   --link tor:tor \
   marcelmaatkamp/screenshot_proxy \
   zqktlwi4fecvo6ri.onion/wiki/index.php/Main_Page \
   zqktlwi4fecvo6ri.onion/wiki/index.php/Main_Page.png \
   1920px
```
