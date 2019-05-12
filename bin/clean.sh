#!/usr/bin/env bash

docker-compose down -v

docker images -a | grep "usul_" | awk '{print $$3}' | xargs docker rmi

rm -rf ./node_modules
rm -rf ./vendor
rm -rf ./public/hot
rm -rf ./public/storage
rm -rf ./public/css
rm -rf ./public/js
rm -rf ./public/mix-manifest.json
