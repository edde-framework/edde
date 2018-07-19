#!/usr/bin/env sh
set -e

echo "Waiting for MySQL"
while ! timeout bash -c "echo > /dev/tcp/edde-mysql/3306"; do sleep 6; done
echo "Waiting for PostgreSQL"
while ! timeout bash -c "echo > /dev/tcp/edde-postgres/5432"; do sleep 6; done

phpunit --coverage-text
