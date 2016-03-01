# OPENAM

## DOCUMENTATION

http://openidm.forgerock.org/doc/bootstrap/install-guide/index.html#full-stack-sample
https://wikis.forgerock.org/confluence/display/openam/Deploy+OpenAM
https://github.com/ForgeRock/docker

## INSTALLATION
```
docker-compose up -d
```
[openam interface](http://host:8090/openam)

| Screenshots of installation |
| ---- |
|![1](images/1.png)|
|Fig 1: Goto [openam interface](http://host:8090/openam) and selct 'Custom Configuration'|
|![2](images/2.png)| 
|Fig 2: make a new password for user 'amadmin' |
|![3](images/3.png)|
|Fig 3:  |
|![4](images/4.png)|
|Fig 4:   |
|![5](images/5.png)|
|Fig 5:   |
|![6](images/6.png)|
|Fig 6:   |
|![7](images/7.png)|
|Fig 7:   |
|![8](images/8.png)|
|Fig 8:   |
|![9](images/9.png)|
|Fig 9:   |
|![10](images/10.png)|
|Fig 10:   |
|![11](images/11.png)|
|Fig 11:   | 
https://github.com/ForgeRock/docker/blob/master/opendj-nightly/Dockerfile
   --baseDN "dc=example,dc=com"

### opendj

| Port | Function  |
| ---- |-----|
| 389  | ldap  |
| 636  | sldap|
| 4444 | task is scheduled through communication over SSL on the administration port, by default 4444|

### openam

| Port | Function  |
| ---- |-----|
| 8080  | ldap  |
| 8081  | sldap|
| 8082 | task is scheduled through communication over SSL on the administration port, by default 4444|

### openidm

## RUNNING
