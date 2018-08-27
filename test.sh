#!/usr/bin/env bash
set -e

echo "Waiting for MySQL"
while !(nc -z edde-mysql 3306); do
#	echo -n .
	sleep 2
done
echo "Waiting for PostgreSQL"
#while !(nc -z edde-postgres 5432); do
#	echo -n .
#	sleep 1
#done

echo "YAHOOO"

#phpunit --coverage-text
