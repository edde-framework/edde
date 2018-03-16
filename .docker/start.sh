#!/usr/bin/env sh
set -e

echo "Waiting for Neo4j"
while ! timeout bash -c "echo > /dev/tcp/neo4j/7687"; do sleep 6; done > /dev/null
echo "Waiting for MySQL"
while ! timeout bash -c "echo > /dev/tcp/mysql/3306"; do sleep 6; done > /dev/null
echo "Waiting for PostgreSQL"
while ! timeout bash -c "echo > /dev/tcp/postgres/5432"; do sleep 6; done > /dev/null

ssh-keygen -A
/usr/sbin/sshd
/usr/sbin/php-fpm7
/usr/sbin/nginx
tail -f /dev/null
