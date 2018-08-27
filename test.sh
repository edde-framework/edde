#!/usr/bin/env bash
set -e

echo "Waiting for MySQL"
#while ! timeout bash -c "echo > /dev/tcp/edde-mysql/3306"; do sleep 1; done
sleep 1
echo "Waiting for Postgres"
#while ! timeout bash -c "echo > /dev/tcp/edde-postgres/5432"; do sleep 1; done
sleep 6

phpunit --coverage-text
