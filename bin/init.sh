#!/usr/bin/env bash

docker-compose up -d

docker-compose run --rm usul_php-cli composer install
docker-compose run --rm usul_php-cli ./artisan migrate

docker-compose run --rm usul_node yarn
docker-compose run --rm usul_node yarn production
