#!/bin/bash
CONTAINER_NAME=orthanc-server-dev
ROOT_DIR=$(dirname "$(realpath "$0")")

docker stop $CONTAINER_NAME >/dev/null 2>&1 || true
docker rm $CONTAINER_NAME >/dev/null 2>&1 || true
docker build -t $CONTAINER_NAME . -f Dockerfile.dev
docker run -it --rm \
  -d \
  -p 3100:80 \
  -v $ROOT_DIR/server/src:/var/www/html \
  -v $ROOT_DIR/server/game-data:/var/www/game-data \
  --env-file .env.dev \
  --name $CONTAINER_NAME \
  $CONTAINER_NAME
