# Dockerfile

Basic idea is to build a fresh image based on [Alpine Linux](https://alpinelinux.org/about/) to keep things small; there are plenty of images
which are good, but quite complex; custom image is a bit better as you have everything under control and it's not hard to maintain this kind of image.

Also it's simpler use custom image to solve some common problems in Docker
like [PID 1](https://blog.phusion.nl/2015/01/20/docker-and-the-pid-1-zombie-reaping-problem/). To prevent [zombie processes](https://en.wikipedia.org/wiki/Zombie_process)
in the image, we have to run proper [init system](http://smarden.org/runit/). Even there are images solving this problem, it's a bit cleaner to
use existing system and understand, how the container works.

> `.docker` folder will hide all Docker related stuff to keep things clear.

?> **.docker/Dockerfile**

```dockerfile
FROM alpine:3.7

ENV LANG="en_US.UTF-8" \
    LANGUAGE="en_US:en" \
    LC_ALL="en_US.UTF-8"

# prepare the base filesystem
COPY .docker/rootfs /

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
