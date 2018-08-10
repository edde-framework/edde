#!/usr/bin/env bash
set -e

echo "Waiting for MySQL"
while ! (telnet edde-mysql 3306 > /dev/null 2>&1); do
	echo -n .
	sleep 1
done
echo "Waiting for PostgreSQL"
while ! (telnet edde-postgres 5432 > /dev/null 2>&1); do
	echo -n .
	sleep 1
done

phpunit --coverage-text
