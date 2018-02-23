#!/usr/bin/env bash
set -e

echo "zend_extension=xdebug.so" > /etc/php7/conf.d/xdebug.ini

echo "Starting PHP-FPM"
/usr/sbin/php-fpm7
echo "Starting nginx"
/usr/sbin/nginx
