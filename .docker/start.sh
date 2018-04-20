#!/usr/bin/env sh
set -e

echo "starting sshd"
/usr/sbin/sshd
echo "starting php-fpm"
/usr/sbin/php-fpm7
echo "starting nginx"
/usr/sbin/nginx
echo "yaaay!"
tail -f /dev/null
