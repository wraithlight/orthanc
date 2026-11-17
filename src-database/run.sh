#!/bin/bash
source ./data.sh

docker stop $CONTAINER_NAME >/dev/null 2>&1 || true
docker rm $CONTAINER_NAME >/dev/null 2>&1 || true
docker build -t $CONTAINER_NAME -f ../Dockerfile.db .
docker run \
    -e MYSQL_DATABASE=$DATABASE_NAME \
    -e MYSQL_ROOT_PASSWORD=$SQL_ROOT_PASSWORD \
    -e MYSQL_USER=$SQL_USERNAME \
    -e MYSQL_PASSWORD=$SQL_PASSWORD \
    -d \
    -p $OUT_PORT:$IN_PORT \
    --name $CONTAINER_NAME \
    --network $NETWORK_NAME \
    $CONTAINER_NAME
