#!/usr/bin/env bash
set -e

docker build -f .docker/Dockerfile -t edde-api ../
docker-compose -f .docker/dc.devel.yml up
