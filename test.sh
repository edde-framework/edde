#!/usr/bin/env bash
set -e

export IMAGE_TEST=edde-test
export PROJECT=edde
export CI_COMMIT_REF_NAME=test

docker build -f .docker/test/Dockerfile -t $IMAGE_TEST .
docker-compose -f .docker/test/docker-compose.yml up -d
docker exec $PROJECT-$CI_COMMIT_REF_NAME ./test.sh
docker-compose -f .docker/test/docker-compose.yml down --volume
