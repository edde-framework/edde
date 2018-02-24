#!/usr/bin/env bash
set -e

docker build -t edde-doc -f .docker/doc/Dockerfile .
docker-compose -f .docker/doc/docker-compose.yml up -d
