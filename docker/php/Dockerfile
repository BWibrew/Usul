FROM php:7.3-fpm

# Install dependencies
RUN apt-get update -yq \
    && apt-get install -yq --no-install-recommends libpng-dev \
    && docker-php-ext-install pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Add user for laravel application
RUN usermod -u 1000 www-data

USER www-data

WORKDIR /var/www
