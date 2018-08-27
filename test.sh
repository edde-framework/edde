#!/usr/bin/env bash
set -e

echo "Waiting for MySQL"
while ! timeout bash -c "echo > /dev/tcp/edde-mysql/3306"; do sleep 1; done
echo "Waiting for Postgres"
while ! timeout bash -c "echo > /dev/tcp/edde-postgres/5432"; do sleep 1; done

phpunit --coverage-text
