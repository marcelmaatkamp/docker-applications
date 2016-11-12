# alpine-xfce4-xrdp on rackspace getcarina.com

## Install Carina
```
$ brew install carina dvm
$ echo "export CARINA_USERNAME=m.maatkamp@gmail.com
export CARINA_APIKEY=<<API_KEY>>
[[ -s \"$(brew --prefix dvm)/dvm.sh\" ]] && source \"$(brew --prefix dvm)/dvm.sh\"
[[ -s \"$(brew --prefix dvm)/bash_completion\" ]] && source \"$(brew --prefix dvm)/bash_completion\"" >> ~/.bash_profile
```
## Define new cluseter
screenshot carina 'desktop' cluster

## Run desktop on cluster
```
$ eval "$( carina env desktop )"
$ docker-compose up -d
```
