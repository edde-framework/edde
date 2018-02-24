#!/usr/bin/env bash
set -e

docker build -f .docker/Dockerfile -t edde-doc ../
docker-compose -f .docker/dc.devel.yml up
