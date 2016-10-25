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

# Import certificate

## Mac
```
$ sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain mitm.cert
```
