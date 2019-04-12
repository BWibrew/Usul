FROM php:7.3-fpm

# Install dependencies
RUN apt-get update -yqq && \
    apt-get upgrade -yqq && \
    apt-get install -yqq --no-install-recommends \
        curl \
        libmemcached-dev \
        libz-dev \
        libpq-dev \
        libjpeg-dev \
        libpng-dev \
        libfreetype6-dev \
        libssl-dev \
        libmcrypt-dev && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql && \
    docker-php-ext-configure gd \
        --with-jpeg-dir=/usr/lib \
        --with-freetype-dir=/usr/include/freetype2 && \
    docker-php-ext-install gd

# Add user for laravel application
RUN usermod -u 1000 www-data

USER www-data

WORKDIR /var/www
