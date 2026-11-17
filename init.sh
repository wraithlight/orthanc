docker network inspect ORTHANC_Internal >/dev/null 2>&1 || \
    docker network create --driver bridge ORTHANC_Internal