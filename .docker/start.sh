#!/usr/bin/env sh
set -e

/usr/sbin/sshd
/usr/sbin/php-fpm7
/usr/sbin/nginx
tail -f /dev/null
