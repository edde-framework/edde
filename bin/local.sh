#!/usr/bin/env bash
set -e

[ "$UID" -eq 0 ] || exec sudo "$0" "$@"

echo "Starting Development Environment!"
docker build -t edde --file .docker/Dockerfile .
docker-compose -f .docker/dc.local.yml up -d
docker exec -it edde bash
docker-compose -f .docker/dc.local.yml down --volumes
echo "Finished!"
