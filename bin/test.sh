#!/usr/bin/env bash
set -e

export IMAGE_TEST=edde-test
export PROJECT=edde
export CI_COMMIT_REF_NAME=test

echo "Building"
docker build -f test/.docker/Dockerfile -t $IMAGE_TEST .
echo "Starting"
docker-compose -f test/.docker/dc.test.yml up -d
echo "Executing tests"
docker exec $PROJECT-$CI_COMMIT_REF_NAME /opt/app/test.sh
echo "Yaay!"
docker-compose -f test/.docker/dc.test.yml down --volume
