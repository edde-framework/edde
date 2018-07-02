# Docker Compose

This is set of useful docker-compose files used for various situations. They can be improved, but
in general it's enough. 

?> Keep this as general recommendation of how you can build `docker-compose.yml` files; they will grow
with your application as more services will get involved (Redis, ...). 

!> Be careful about tabs versus spaces when copying content of the yaml file as it's quite sensitive
to tabs and spaces.

## docker.local.yml

Local development support; binds volumes to source code, enables `xdebug` and ssh server.
  
?> **.docker/docker.local.yml**

```yaml
version: '2'
services:
    sandbox:
        # container name is used intentionally to have easier access to logs
        container_name: sandbox
        # instead of container hash container will have nice hostname
        hostname: sandbox-local
        # name of image build before execution of this file
        image: sandbox:local
        # magical section contaning all the variables needed to run the application
        environment:
            # mandatory variables
            - SANDBOX_VERSION=local
            # it's important to name database host as it could "randomly" connect to wrong host 
            - SANDBOX_DATABASE_HOST=sandbox-postgres
            - SANDBOX_DATABASE_USER=sandbox
            - SANDBOX_DATABASE_PASSWORD=my-secret-password
            # other variables
            - XDEBUG_IP=${XDEBUG_IP}
            # for PHPStorm users this will simplify path mapping
            - PHP_IDE_CONFIG=serverName=sandbox
            # this safe password is enough
            - ROOT_PASSWORD=root:1234
        ports:
            # map SSH port outside of a container
            - "2622:22"
            # backend port
            - "2680:80"
        volumes:
            # this is generally necessary
            - ../backend:/sandbox/backend
            # enable SSH daemon
            - ./localfs/etc/ssh:/etc/ssh
            - ./localfs/etc/service/sshd:/etc/service/sshd
            # control php stuff needed for local development
            - ./localfs/etc/php7/conf.d/00_opcache.ini:/etc/php7/conf.d/00_opcache.ini
            - ./localfs/etc/php7/conf.d/xdebug.ini:/etc/php7/conf.d/xdebug.ini
        networks:
            - sandbox-network

    # use whatever database you want, but Postgres is recommended
    sandbox-postgres:
        image: postgres:10.4
        environment:
            - POSTGRES_USER=sandbox
            - POSTGRES_PASSWORD=my-secret-password
        networks:
            - sandbox-network

    # just handy tool to have an insight into the database
    adminer:
        image: adminer
        networks:
            - sandbox-network
        ports:
            # map adminer port
            - "2600:8080"

networks:
        sandbox-network:
```

## docker.prdel.yml

Production development configuration of docker-compose.

?> **.docker/docker.prdel.yml**

```yaml
version: '2'
services:
    sandbox:
        # container name is used intentionally to have easier access to logs
        container_name: sandbox
        # instead of container hash container will have nice hostname
        hostname: sandbox-prdel
        # name of image build before execution of this file
        image: sandbox:prdel
        # magical section contaning all the variables needed to run the application
        environment:
            # mandatory variables
            - SANDBOX_VERSION=prdel
            # it's important to name database host as it could "randomly" connect to wrong host 
            - SANDBOX_DATABASE_HOST=sandbox-postgres
            - SANDBOX_DATABASE_USER=sandbox
            - SANDBOX_DATABASE_PASSWORD=my-secret-password
        ports:
            # backend port
            - "2680:80"
        networks:
            - sandbox-network

    # use whatever database you want, but Postgres is recommended
    sandbox-postgres:
        image: postgres:10.4
        environment:
            - POSTGRES_USER=sandbox
            - POSTGRES_PASSWORD=my-secret-password
        networks:
            - sandbox-network

    # just handy tool to have an insight into the database
    adminer:
        image: adminer
        networks:
            - sandbox-network
        ports:
            # map adminer port
            - "2600:8080"

networks:
        sandbox-network:
```

**Previous**: [Local Filesystem](/getting-started/localfs) | **Next**: [Bin](/getting-started/bin)
