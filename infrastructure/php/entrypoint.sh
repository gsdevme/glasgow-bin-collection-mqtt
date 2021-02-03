#!/bin/sh
set -e

if [ "${APP_ENV}" != "dev" ]; then
  if [ "$EUID" = "0" ]
  then
    echo "Warning, container is running is root, this is not advised"
    echo "Warning, container is running is root, this is not advised"
    echo "Warning, container is running is root, this is not advised"
    echo "Warning, container is running is root, this is not advised"
    echo "Warning, container is running is root, this is not advised"
    echo "Warning, container is running is root, this is not advised"
    echo "Warning, container is running is root, this is not advised"
    echo "YOU SHOULD NOT RUN ANY CONTAINER AS ROOT"
    echo "YOU SHOULD NOT RUN ANY CONTAINER AS ROOT"
    echo "YOU SHOULD NOT RUN ANY CONTAINER AS ROOT"
  fi
fi

exec "$@"
