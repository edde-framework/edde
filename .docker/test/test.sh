#!/usr/bin/env bash
set -e

echo "Waiting for neo4j"
while ! timeout bash -c "echo > /dev/tcp/neo4j/7687"; do sleep 4; done
echo "Waiting for mysql"
while ! timeout bash -c "echo > /dev/tcp/mysql/3306"; do sleep 4; done
echo "Waiting for postgres"
while ! timeout bash -c "echo > /dev/tcp/postgres/5432"; do sleep 4; done

lib/bin/phpunit --coverage-text --colors=never --configuration phpunit.xml --bootstrap tests/src/loader.php
