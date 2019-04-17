FROM alpine:3.9

# Install dependencies
RUN apk --update add \
    sudo \
    bash \
    git \
    wget \
    curl \
    ca-certificates \
    nodejs \
    nodejs-npm \
    yarn

# Install PHP
ADD https://dl.bintray.com/php-alpine/key/php-alpine.rsa.pub /etc/apk/keys/php-alpine.rsa.pub

RUN echo "http://dl-cdn.alpinelinux.org/alpine/v3.9/main" > /etc/apk/repositories && \
    echo "http://dl-cdn.alpinelinux.org/alpine/v3.9/community" >> /etc/apk/repositories && \
    echo "@php https://dl.bintray.com/php-alpine/v3.9/php-7.3" >> /etc/apk/repositories && \
    apk --update add \
    php@php \
    php-curl@php \
    php-dom@php \
    php-json@php \
    php-mbstring@php \
    php-openssl@php \
    php-pdo@php \
    php-pdo_sqlite@php \
    php-phar@php \
    php-session@php \
    php-zlib@php && \
    ln -s /usr/bin/php7 /usr/bin/php && \
    rm /var/cache/apk/*

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('SHA384', 'composer-setup.php') === trim(file_get_contents('https://composer.github.io/installer.sig'))) { echo 'Installer verified'; } else { echo 'Installer invalid'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');"

# ensure www-data user exists
RUN addgroup -g 1000 -S www-data && \
    adduser -u 1000 -D -S -G www-data www-data && \
    mkdir -p /var/www && \
    chown -R www-data:www-data /var/www

USER www-data

CMD tail -f /dev/null
