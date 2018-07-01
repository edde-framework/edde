# Sandbox

?> This is one way, how you can build production ready Docker container with your application.

?> To keep log rotate complex and not maintainable, everything is redirected to `stderr` and `stdout`, thus you can use
any service to hook on `docker log` and process logs if needed.

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

Fix permissions to bound them to `nginx` and `www-data`.

?> **.docker/rootfs/etc/bootstrap/10-fixperms.sh**

```bash
#!/usr/bin/env sh
set -e

cd /backend/backend
chown -R nginx:www-data .
```

### nginx.conf

There are plenty ways, how to configure nginx, this is one of them.

?> **.docker/rootfs/etc/nginx/nginx.conf**

```
user                nginx www-data;
worker_processes    1;
pid                 /var/run/nginx.pid;
daemon              off;
error_log           /dev/stdout info;

events {
	worker_connections  1024;
}

http {
    include             /etc/nginx/mime.types;
    default_type        application/octet-stream;
    access_log          /dev/stdout;
	server_tokens       off;
	sendfile            on;
    keepalive_timeout   65;
    gzip                off;

	# where PHP server(s) lives; example, how to run more upstream
	# servers
	upstream sandbox {
		server unix:/var/run/php7-fpm-01.sock;
		server unix:/var/run/php7-fpm-02.sock;
    }

	server {
	    listen 80;

		location / {
			client_max_body_size    0;
			client_body_buffer_size 128k;
			include                 fastcgi_params;
			fastcgi_param           SCRIPT_FILENAME /sandbox/backend/index.php;
	        fastcgi_pass            sandbox;
	        fastcgi_read_timeout    1d;
		}
	}
}
```

### php-fpm.conf

Yet another way how to configure PHP-FPM

?> **.docker/rootfs/etc/php7/php-fpm.conf**

```ini
[global]
pid = /run/php-fpm.pid
error_log = /dev/stderr
emergency_restart_threshold = 10
emergency_restart_interval = 1m
process_control_timeout = 10s

[sandbox-01]
listen = /var/run/php7-fpm-01.sock
listen.backlog = -1
listen.owner = nginx
listen.group = www-data
listen.mode = 0660
access.log = /proc/self/fd/2
chdir = /sandbox/backend
catch_workers_output = no
clear_env = no
user = nginx
group = www-data
pm = dynamic
pm.max_children = 6
pm.start_servers = 3
pm.min_spare_servers = 3
pm.max_spare_servers = 5
pm.max_requests = 500
request_terminate_timeout = 0
php_value[memory_limit] = 128M
php_value[max_execution_time] = 0

[sandbox-02]
listen = /var/run/php7-fpm-02.sock
listen.backlog = -1
listen.owner = nginx
listen.group = www-data
listen.mode = 0660
access.log = /proc/self/fd/2
chdir = /sandbox/backend
catch_workers_output = no
clear_env = no
user = nginx
group = www-data
pm = dynamic
pm.max_children = 6
pm.start_servers = 3
pm.min_spare_servers = 3
pm.max_spare_servers = 5
pm.max_requests = 500
request_terminate_timeout = 0
php_value[memory_limit] = 128M
php_value[max_execution_time] = 0
```

### nginx/run

Startup script for nginx; script must stay alive or `runit` service try to restart script again and again. 

?> **.docker/rootfs/etc/service/nginx/run**

```bash
#!/usr/bin/env sh
set -e

exec /usr/sbin/nginx
```

### php-fpm/run

Startup script for php-fpm; same for nginx - script must stay alive or `runit` will try to restart the service. 

?> **.docker/rootfs/etc/service/php-fpm/run**

```bash
#!/usr/bin/env sh
set -e

# -F is foreground
exec /usr/sbin/php-fpm7 -F
```

### healthcheck

Simple healthcheck script to keep some info about the container.

?> **bin/healtcheck**

```php
#!/usr/bin/env php
<?php
	declare(strict_types=1);
	try {
		// it's good to use some address which required database access (or create proprietary healthcheck url)
		if (file_get_contents('http://0.0.0.0/upgrade.upgrade/version') === false) {
			throw new RuntimeException('Backend is not available!');
		}
		exit(0);
	} catch (Throwable $exception) {
		echo $exception->getMessage() . "\n";
		exit(1);
	}
```

## Local Filesystem

Because there is a little difference between local development and production environment, modification are made on container
level.

?> This is a bit more painful as docker-compose file is used; basic idea is to turn off `opcache` and enable `xdebug`. 

### 00_opcache.ini

Disable opcache by emptying it's config file.

?> **.docker/localfs/etc/php7/conf.d/00_opcache.ini**

```ini
# just this empty file
```

### xdebug.ini

Enable xdebug by this ini file.

?> **.docker/localfs/etc/php7/conf.d/xdebug.ini**

```ini
zend_extension = xdebug.so
xdebug.remote_enable = 1
xdebug.remote_autostart = 1
xdebug.remote_host = ${XDEBUG_IP}
```

### ssh/run

It's useful to have access to container through ssh as it's much simpler to execute tests in this way.

?> **.docker/localfs/etc/service/sshd/run**

```bash
#!/usr/bin/env sh
set -e

# install ssh server
apk add openssh
# fix permissions as without this ssh damon won't start 
chmod 600 -R /etc/ssh
# setup root password from env. variable; it's good enough to use some fuckin' simple password
# something like "root:1234" as the container will be available just for you
echo "${ROOT_PASSWORD}" | chpasswd
# start the deamon
exec /usr/sbin/sshd -D >/dev/null 2>&1
```

### sshd_config

SSH server setup.

?> **.docker/localfs/etc/ssh/sshd_config**

```
# to speed up things a bit
UseDNS no
# yes, in this case we want enable root user
PermitRootLogin yes
# important to enable remote access for example from PHPStorm
Subsystem sftp /usr/lib/ssh/sftp-server
```

### ssh_host_rsa_key

It's good to use one private key to prevent questions about SSH connection to unknown host.

!> These keys are just for local use, NEVER use them on production as these are private keys.

?> **.docker/localfs/etc/ssh/ssh_host_rsa_key**

```
-----BEGIN RSA PRIVATE KEY-----
MIIEpQIBAAKCAQEAsyryreglP/NCVLo+Asent3BaKDHU1ZwgiASvyFDW2o/f4dQ+
IChYxV/TxUeNEvV0r3E0gGEzxp4E3R4JLVzV2ryVvl/c+fpDqx3t6x7DOcLzL/mj
3VC9d/3b6ayRaIqRNgieHyS3FHbdcK7fgd9fOQjxSgCQTIElc6H3EZsGt8OL1iWB
IwUbohD7gyv1fybaX8iunCpruSIZvYKiiKaAeMx0wNaTZ9A15TkqvSQFlVcisT8o
HgBZgwwDW8nDFFik1grRM/jLHxH6xNTJK2Mjm24rKqqzjO8hCr5NZSGPk/7BuKGs
OHhtXUAQd+r0XdL1IE+D4GFqVhpNUqvQMPvV8wIDAQABAoIBAGMV2Wg7/n3DdmeN
KEY5TJOyWunnxSDtW0Bd9yj9LBVrhBFMY589MPrW6DMuQuK9RG7SWIq3Nv8c3Ou8
dK7VrJ9vtBC4y2ij81BrGOzy8ly7Z+qcUPdQc7gseLZebXw3Rd9wHKJ0l5uFsSlk
TM9yTSMpwud+ME9fDOtKI21lIu31FjpSwEUUz6wcoFnjhObrkg19MJZ69N442wyp
bNhkiW/kvlXlEb2s7y1v54JNrXfBquyJc23Liz2of7GZA5B0h9XIFWBAjZJMTu1B
Mfjt/YZb/sRz7HardWBy5LDyC8Lu55db73T2YTTIzEL8cf5w0t39UNSdKLx4ngl9
Llld8sECgYEA1lroUEYhlKikMD8nL5B+KIJqC3LaJZFDSnUv2gSVRo1xy9tkdFNx
xNyCus1EF81HdlkJfhhpBZNrUUAzVpsK3TBSdU8hWrFO0sfdEjTcj3pbQSyZp5oh
kMRP913LTNtrqDohp3Jj4LSjQP3chuB46NTqNCzsVFaZ2rvwDE+QSmsCgYEA1fn5
kDCCh4+k0Pu4Yr9Ggkd+PNwZ+fo6ytYJbY6KFFcCv8M1GPxc/p7/IBtY/o25mIYP
v7m2ANXrW9YxfvgaN1fbCAIuyM/mWcl9acLLeGHgG3XP1RdVqGAjp8s5PWcLx0MW
Y6QM/Zdy52iXTmtVsBXTrPqHGg4SGiRmN2ONFJkCgYEAsAPwFdKwynh8cl25WLZm
0e7dE0+ZUBXrUp3N0FHJHikRk5sw7tCCcGu+MZRSYNUg5E6Sm+eBfaGjExIL1gb0
MdL3MvrqYaWNG0og/01G+842Vts/xT+sZkq9e1uakB7xVS9+6RfmaeMK11heGKcE
wfPr6TImUc7XAaUnpYRD8McCgYEA1V2skzEsF40O5iz7Ucw1vNcZdApuBKyWT4ha
YlqOKXYSEbHPkpijCmr1L8jVVw9vkD2uGppOeErXo/3T4S34xlLe3/99M1TL96BJ
ZFqPlfzTAc7abTwVeA5Vv42QCEBmqE2nV7hQE9cfBs1ugw3Ypfs91fEDIuIA/vxo
yLaGCZECgYEAg/olGfSjymY/1EHwojSPhw9SZ/PHgAsRdlS3GcrY4w9NCpLVLUbn
+oVcQWcMQvA6JkK7OjLAdYnCphFVquOo7gRqvYI1VcEJsB7bnb0O4REK9tSaUVj8
eRaCqDMfhyOs+q8jfO5zbkz0YPP0Lu5ys8fC2koPFg4wXAZD0z+QEX4=
-----END RSA PRIVATE KEY-----
```
