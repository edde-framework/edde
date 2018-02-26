#!/usr/bin/env bash
set -e

docker build -f .docker/Dockerfile -t edde-test ../
docker-compose -f .docker/dc.devel.yml up -d
docker exec -it edde-test ash
docker-compose -f .docker/dc.devel.yml down --volumes
