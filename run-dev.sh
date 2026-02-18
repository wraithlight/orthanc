#!/bin/bash
CONTAINER_NAME=orthanc-server-dev
ROOT_DIR=$(dirname "$(realpath "$0")")

docker stop $CONTAINER_NAME >/dev/null 2>&1 || true
docker rm $CONTAINER_NAME >/dev/null 2>&1 || true
docker build -t $CONTAINER_NAME . -f Dockerfile.dev
docker run -it --rm \
  -d \
  -p 3100:80 \
  -v $ROOT_DIR/src-server/src:/var/www/html \
  -v $ROOT_DIR/src-server/game-data:/var/www/game-data \
  -v $ROOT_DIR/src-frontend/public:/var/www/html/fe-assets \
  -v $ROOT_DIR/docs-api:/var/www/docs-api \
  --env-file .env.dev \
  --name $CONTAINER_NAME \
  $CONTAINER_NAME
