# MITMPROXY

# Start proxy
```
$ docker-compose up -d
```

# Extract certificate
```
$ docker-compose run mitmproxy cat /root/.mitmproxy/mitmproxy-ca-cert.cer > mitm.cer
```

# Add proxy
!(image)[https://github.com/marcelmaatkamp/docker-applications/blob/master/mitmproxy/images/Schermafbeelding%202016-10-25%20om%2019.49.50.png?raw=true]

# Import certificate

## Import on a Mac
```
$ sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain mitm.cert
```

# View logging
```
$ docker-compose logs -f
```
