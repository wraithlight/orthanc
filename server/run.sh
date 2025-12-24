docker build -t orthanc-server .

docker run -it --rm \
  -d \
  -p 3100:80 \
  -v $(pwd)/src:/var/www/html \
  -v $(pwd)/data:/var/www/html/data \
  -v $(pwd)/game-data:/var/www/html/game-data \
  orthanc-server
