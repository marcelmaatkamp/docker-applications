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
|Fig 1: Goto [openam interface](http://host:8090/openam) and select 'Custom Configuration'|
|![2](images/2.png)| 
|Fig 2: make a new password for user 'amadmin'|
|![3](images/3.png)|
|Fig 3: We're not doing SSO right now so leave these settings for now and click 'Next'|
|![4](images/4.png)|
|Fig 4: connect opendj port 386, use 'password' as password, Root Suffix is 'dc=example,dc=com' and click 'Next'|
|![5](images/5.png)|
|Fig 5: use the same values as in Fig 4 and click 'Next' |
|![6](images/6.png)|
|Fig 6: Leave these empty and click 'Next'|
|![7](images/7.png)|
|Fig 7: Generate a nice password and click 'Next'|
|![8](images/8.png)|
|Fig 8: Validate the settings and click 'Next'|
|![9](images/9.png)|
|Fig 9: Settings are successfully applied|
|![10](images/10.png)|
|Fig 10: Login with user 'amadmin' with password from Fig 2|
|![11](images/11.png)|
|Fig 11: And openam is successfully installed| 

opendj
https://github.com/ForgeRock/docker/blob/master/opendj-nightly/Dockerfile
   --baseDN "dc=example,dc=com"

### opendj

#### Configuration
The settings like Root Suffix "dc=example,dc=com" come from the [Dockerfile](https://github.com/ForgeRock/docker/blob/master/opendj-nightly/Dockerfile) and can be overridden with a new Dockerfile accordingly. 

#### Ports
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
