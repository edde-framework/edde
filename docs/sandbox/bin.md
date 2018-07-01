# Bin

Some little pieces to startup the whole thing. 

## bin/local.sh

Starts local environment and login to shell; when you `exit`, container will stop.

?> **bin/local.sh**

```bash
#!/usr/bin/env sh
set -e

# even it's much slower to use pull, it's much safer when production comes in as on
# local there is a same environment as on production
docker build --pull -f .docker/Dockerfile -t sandbox:local .
docker-compose -f .docker/docker.local.yml up -d
# simple way how to keep container running and stop it when you are done :)
docker exec -it sandbox ash
docker-compose -f .docker/docker.local.yml stop
```

## bin/prdel.sh

Starts local production environment; thus `opcache` is enabled, `xdebug` disabled; you can see
epic performance of the application.

?> **bin/prdel.sh**

```bash
#!/usr/bin/env sh
set -e

docker build --pull -f .docker/Dockerfile -t sandbox:prdel .
docker-compose -f .docker/docker.prdel.yml up -d
docker exec -it sandbox ash
docker-compose -f .docker/docker.local.yml stop
```

**Previous**: [Docker Compose](/sandbox/docker-compose) | **Next**: [Backend](/sandbox/backend)
