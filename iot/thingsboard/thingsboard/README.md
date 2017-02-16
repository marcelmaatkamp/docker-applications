```
docker-compose exec ssh sh -c "mkdir -p ~/.ssh && echo `cat ~/.ssh/id_rsa.pub` > ~/.ssh/authorized_keys"
```

```
ssh -D 1080 -p 4848 root@docker
```
