#!/usr/bin/env bash
set -e

export IMAGE_API=edde-api
export IMAGE_DOC=edde-doc

docker build -t $IMAGE_API -f .docker/api/Dockerfile .
docker build -t $IMAGE_DOC -f .docker/doc/Dockerfile .

docker-compose -f .docker/dc.production.yml up --remove-orphans
