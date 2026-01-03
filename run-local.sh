#!/bin/bash
CONTAINER_NAME=orthanc-game-local
ROOT_DIR=$(dirname "$(realpath "$0")")

docker stop $CONTAINER_NAME >/dev/null 2>&1 || true
docker rm $CONTAINER_NAME >/dev/null 2>&1 || true
docker build -t $CONTAINER_NAME . -f Dockerfile.local
docker run -it --rm \
  -d \
  -p 4100:80 \
  --name $CONTAINER_NAME \
  --env-file .env.local \
  $CONTAINER_NAME
