#!/usr/bin/env bash
set -e

[ "$UID" -eq 0 ] || exec sudo "$0" "$@"

echo "Starting Development Environment!"
docker build --pull -t edde --file .docker/Dockerfile .
docker-compose -f .docker/docker.local.yml up -d
docker exec -it edde ash
docker-compose -f .docker/docker.local.yml down --volumes
echo "Finished!"
