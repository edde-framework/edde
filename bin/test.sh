#!/usr/bin/env bash
set -e

export IMAGE_TEST=edde-test
export PROJECT=edde
export CI_COMMIT_REF_NAME=test

echo "Building"
docker build -f .docker/test/Dockerfile -t $IMAGE_TEST .
echo "Starting"
docker-compose -f .docker/test/docker-compose.yml up -d
echo "Executing tests"
docker exec $PROJECT-$CI_COMMIT_REF_NAME /opt/app/test.sh
echo "Yaay!"
docker-compose -f .docker/test/docker-compose.yml down --volume
