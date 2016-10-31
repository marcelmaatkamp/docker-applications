# IPFS

https://github.com/ipfs/go-ipfs

# Installation and start container
```
docker-compose up -d ipfs
```

# Exec
```
docker-compose exec ipfs /usr/local/bin/ipfs <args...>
```

# Swarm 
```
docker-compose exec ipfs /usr/local/bin/ipfs swarm peers
```

# Copy
```
docker cp README.md $(docker-compose ps | grep ^ipfs | awk '{ print $1 }'):/export
docker-compose exec ipfs /usr/local/bin/ipfs add -r /export/README.md
```

# Stop container
```
docker-compose stop ipfs
```

