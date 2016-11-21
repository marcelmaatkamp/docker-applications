# alpine-xfce4-xrdp on rackspace getcarina.com

## Install Carina
```
$ brew install carina dvm
$ echo "export CARINA_USERNAME=<<CARINA_USERNAME>>
export CARINA_APIKEY=<<CARINA_API_KEY>>
[[ -s \"$(brew --prefix dvm)/dvm.sh\" ]] && source \"$(brew --prefix dvm)/dvm.sh\"
[[ -s \"$(brew --prefix dvm)/bash_completion\" ]] && source \"$(brew --prefix dvm)/bash_completion\"" >> ~/.bash_profile
```
## Define new cluster
```
$ carina create desktop
```
![](https://github.com/marcelmaatkamp/docker-applications/blob/master/desktop/alpine-xfce4-xrdp/Schermafbeelding%202016-11-12%20om%2008.20.18.png?raw=true)

## Run desktop on cluster
```
$ eval "$( carina env desktop )"
$ docker-compose up -d
```

## Connect to desktop
![](https://github.com/marcelmaatkamp/docker-applications/blob/master/desktop/alpine-xfce4-xrdp/Schermafbeelding%202016-11-12%20om%2008.32.25.png?raw=true)
![](https://github.com/marcelmaatkamp/docker-applications/blob/master/desktop/alpine-xfce4-xrdp/Schermafbeelding%202016-11-12%20om%2008.49.28.png?raw=true)
