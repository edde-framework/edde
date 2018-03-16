#!/usr/bin/env bash
set -e

echo "Waiting for Neo4j"
while ! timeout bash -c "echo > /dev/tcp/neo4j/7687"; do sleep 6; done > /dev/null
echo "Waiting for MySQL"
while ! timeout bash -c "echo > /dev/tcp/mysql/3306"; do sleep 6; done > /dev/null
echo "Waiting for PostgreSQL"
while ! timeout bash -c "echo > /dev/tcp/postgres/5432"; do sleep 6; done > /dev/null

lib/bin/phpunit
