#!/usr/bin/env sh
set -e

ssh-keygen -A
/usr/sbin/sshd
/usr/sbin/php-fpm7
/usr/sbin/nginx
tail -f /dev/null
