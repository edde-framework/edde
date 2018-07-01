# Sandbox

?> This is one way, how you can build production ready Docker container with your application.

## Dockerfile

We need Dockerfile which will define all the interesting stuff in the image.

Basic idea is to build fresh image on [Alpine Linux](https://alpinelinux.org/about/) to keep things small, but no so much
simple.

To prevent [zombie processes](https://en.wikipedia.org/wiki/Zombie_process) in the image, we have to run proper [init system](http://smarden.org/runit/).

This image also implements simple healthcheck to see, if everything is ok :wink:.

`.docker` folder will hide all Docker related stuff to keep things clear.

?> **src/.docker/Dockerfile**

```dockerfile
FROM alpine:edge

ENV LANG="en_US.UTF-8" \
    LANGUAGE="en_US:en" \
    LC_ALL="en_US.UTF-8"

RUN apk --update upgrade && apk add --no-cache \
	# proper init system; prevents zombie processes
	runit \
	# yay, webserver!
	nginx \
	# we need to download some stuff 'round
	curl \
	# believe in certificates 
	ca-certificates \
	# some of the tools needs this
	unzip \
	# bash rulezz
	bash \
	# envsubst is hidden in this package needed to replace env variables in files
	gettext \
	# because of composer
	git \
	# a lot of php stuff
	php7 php7-fpm php7-gd php7-pdo_pgsql php7-bcmath php7-xml php7-curl php7-intl \
	php7-mbstring php7-xml php7-zip php7-zlib php7-openssl php7-json php7-fileinfo \
	php7-tokenizer php7-dom php7-xmlwriter php7-xmlreader php7-ctype php7-phar \
	# yes, both of them as they're updated in prod/local environemnts
	php7-opcache php7-xdebug

# prepare the base filesystem
COPY .docker/rootfs /

# init scripts must be executable
RUN chmod 0750 -R /etc/service
RUN chmod 0750 -R /etc/bootstrap
# setup executable on boot script
RUN chmod +x /bootstrap.sh

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
# turboplugin for composer to install dependencies much faster
RUN composer global require hirak/prestissimo --prefer-dist --no-progress

RUN rm -rf /var/cache/apk/*

WORKDIR /sandbox/bin
COPY bin/healthcheck healthcheck
RUN chmod +x healthcheck

# sandbox manager configuration
WORKDIR /sandbox/backend
COPY backend/ .

ENTRYPOINT ["/bootstrap.sh"]

HEALTHCHECK --timeout=3s --start-period=10s --interval=5s CMD ["/sandbox/bin/healthcheck"]

# formally expose ports used
EXPOSE 80
```

## Root Filesystem

To keep things simple and small, we'll copy all necessary stuff by a one command.

?> Order of files in bootstrap folder is important.

### bootstrap.sh

Bootstrap is simple bash script responsible for bootstrapping the container.

?> **.docker/rootfs/bootstrap.sh**

```bash
#!/usr/bin/env sh

# execute all scripts being part of bootstrap process; this should NOT execute any
# daemon processes!
for script in /etc/bootstrap/*
do
	$script
    status=$?
    if [ $status != 0 ];
    then
        echo >&2 "$script: failed with return value $?"
        exit $status
    fi
done
# execute runit daemon to take care about process management
exec /sbin/runsvdir -P /etc/service
```

### 00-envsubst.sh

A bit of magic providing env variables into an application in a safe way.

?> **.docker/rootfs/etc/bootstrap/00-envsubst.sh**

```bash
#!/usr/bin/env sh

set -e

cd /sandbox/backend
# create config file from template using values from environment variables
cat config.ini.template | envsubst > config.ini
```

### 05-composer.sh

Execute composer and install production dependencies.

?> **.docker/rootfs/etc/bootstrap/05-composer.sh**

```bash
#!/usr/bin/env sh

set -e

cd /sandbox/backend
if [ -f compose.json ]; then
	composer install --prefer-dist --no-progress
fi
```

### 10-fixperms.sh

Fix permissions to bound them to `nginx and `www-data`.

?> **.docker/rootfs/etc/bootstrap/10-fixperms.sh**

```bash
#!/usr/bin/env sh

set -e

cd /backend/backend
chown -R nginx:www-data .
```
