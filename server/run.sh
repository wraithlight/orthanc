#!/bin/bash
CONTAINER_NAME=orthanc-server
ROOT_DIR=$(dirname "$(realpath "$0")")

docker stop $CONTAINER_NAME >/dev/null 2>&1 || true
docker rm $CONTAINER_NAME >/dev/null 2>&1 || true
docker build -t $CONTAINER_NAME .
docker run -it --rm \
  -d \
  -p 3100:80 \
  -v $ROOT_DIR/src:/var/www/html \
  -v $ROOT_DIR/data:/var/www/data \
  -v $ROOT_DIR/game-data:/var/www/game-data \
  --name $CONTAINER_NAME \
  $CONTAINER_NAME
