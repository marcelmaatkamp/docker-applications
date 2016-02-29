# Take screenshots of onion urls
### Start a tor daemon
```
$ docker run -d \
    --name tor \
    -p 9050:9050 \
    marcelmaatkamp/alpine-tor:latest
```
### Take an screenshot image of a hidden service onion url
```
$ docker run -ti \
   -v $PWD/images:/srv \
   --link tor:tor \
   marcelmaatkamp/screenshot_proxy \
   zqktlwi4fecvo6ri.onion/wiki/index.php/Main_Page \
   zqktlwi4fecvo6ri.onion/wiki/index.php/Main_Page.png \
   1920px
```
### Actual screenshot of zqktlwi4fecvo6ri.onion
![image](https://raw.githubusercontent.com/marcelmaatkamp/docker-applications/master/web/screenshot/ubermuda_screenshot/images/zqktlwi4fecvo6ri.onion/wiki/index.php/Main_Page.png)
