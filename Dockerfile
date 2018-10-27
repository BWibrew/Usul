FROM php:7.2-apache

RUN apt-get update -yq \
    && apt-get install -yq --no-install-recommends libpng-dev \
    && docker-php-ext-install pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN a2enmod rewrite

COPY . /var/www

COPY /docker/apache/default.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www
