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

![1](images/1.png)
![2](images/2.png)
![3](images/3.png)
![4](images/4.png)
![5](images/5.png)
![6](images/6.png)
![7](images/7.png)
![8](images/8.png)
![9](images/9.png)

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