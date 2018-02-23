#!/usr/bin/env bash
set -e

echo "zend_extension=xdebug.so" > /etc/php7/conf.d/xdebug.ini

#while ! timeout bash -c "echo > /dev/tcp/0.0.0.0/80"; do sleep 6; done
#lib/bin/phpunit --coverage-text --colors=never --configuration phpunit.xml --bootstrap tests/src/loader.php
