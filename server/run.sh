#!/bin/bash
CONTAINER_NAME=orthanc-server

docker stop $CONTAINER_NAME >/dev/null 2>&1 || true
docker rm $CONTAINER_NAME >/dev/null 2>&1 || true
docker build -t $CONTAINER_NAME .
docker run -it --rm \
  -d \
  -p 3100:80 \
  -v $(pwd)/src:/var/www/html \
  -v $(pwd)/data:/var/www/html/data \
  -v $(pwd)/game-data:/var/www/html/game-data \
  --name $CONTAINER_NAME \
  $CONTAINER_NAME
