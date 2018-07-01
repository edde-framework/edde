# Root Filesystem

To keep things simple and small, we'll copy all necessary stuff by a one command.

?> Order of files in `bootstrap` folder is important.

## bootstrap.sh

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

## 00-envsubst.sh

A bit of magic providing env variables into an application in a safe way.

?> **.docker/rootfs/etc/bootstrap/00-envsubst.sh**

```bash
#!/usr/bin/env sh
set -e

cd /sandbox/backend
# create config file from template using values from environment variables
cat config.ini.template | envsubst > config.ini
```

## 05-composer.sh

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

## 10-fixperms.sh

Fix permissions to bound them to `nginx` and `www-data`.

?> **.docker/rootfs/etc/bootstrap/10-fixperms.sh**

```bash
#!/usr/bin/env sh
set -e

cd /sandbox/backend
chown -R nginx:www-data .
```

## nginx.conf

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

## php-fpm.conf

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

## nginx/run

Startup script for nginx; script must stay alive or `runit` service try to restart script again and again. 

?> **.docker/rootfs/etc/service/nginx/run**

```bash
#!/usr/bin/env sh
set -e

exec /usr/sbin/nginx
```

## php-fpm/run

Startup script for php-fpm; same for nginx - script must stay alive or `runit` will try to restart the service. 

?> **.docker/rootfs/etc/service/php-fpm/run**

```bash
#!/usr/bin/env sh
set -e

# -F is foreground
exec /usr/sbin/php-fpm7 -F
```

## healthcheck

Simple healthcheck script to keep some info about the container.

?> **bin/healthcheck**

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

**Previous**: [Dockerfile](/sandbox/dockerfile) | **Next**: [Local Filesystem](/sandbox/localfs)
