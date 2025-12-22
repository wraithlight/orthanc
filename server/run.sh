docker build -t orthanc-server .

# docker run -p 3100:80 orthanc-server

docker run -it --rm \
  -p 3100:80 \
  -v $(pwd)/src:/var/www/html \
  -v $(pwd)/data:/var/www/html/data \
  orthanc-server
