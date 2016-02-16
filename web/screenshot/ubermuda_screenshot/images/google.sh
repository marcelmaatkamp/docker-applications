#!/bin/bash
docker run -v $PWD:/srv svendowideit/screenshot http://www.google.com/ google.com.png 1920px

