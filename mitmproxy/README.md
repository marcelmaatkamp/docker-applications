# MITMPROXY
Runs https://mitmproxy.org in a docker container (thanks to https://github.com/danielguerra69/alpine-mitmproxy)

![mitmproxy](https://mitmproxy.org/images/mitmproxy.png)

# Start proxy
```
$ docker-compose up -d
```

# Extract certificate
```
$ docker-compose run mitmproxy cat /root/.mitmproxy/mitmproxy-ca-cert.cer > mitm.cer
```

# Import certificate

## Import certificate on a Mac
```
$ sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain mitm.cert
```

# Add proxy
![image](https://github.com/marcelmaatkamp/docker-applications/blob/master/mitmproxy/images/Schermafbeelding%202016-10-25%20om%2019.49.50.png?raw=true)

# View logging
```
$ docker-compose logs -f
```
