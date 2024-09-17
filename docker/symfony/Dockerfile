FROM php:8.2-fpm-alpine@sha256:a16bc43404e602adb0f8f9662a1319f83d85daf23110bf7e02152a8a0e3d7e5e

COPY --from=composer:latest@sha256:8008ba4d8723edf5f3566bd94e9330a5cdff3d6125ed34a8502718f8d2289515 /usr/bin/composer /usr/bin/composer

# Install DB driver for PHP => pdo pdo_mysql
RUN set -xe \
    && apk add --no-cache --update --virtual .phpize-deps $PHPIZE_DEPS \
    && apk add bash git \
    && apk add linux-headers \
    && apk add icu-dev \
    && pecl install xdebug-3.3.1 \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install pdo pdo_mysql intl \
    && rm -rf /usr/share/php \
    && rm -rf /tmp/* \
    && apk del  .phpize-deps \
    && apk add bind-tools

# Install xdebug
RUN mv "/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini" "/usr/local/etc/php/conf.d/docker-php-ext-xdebug.off"

COPY docker/symfony/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN ping -c 1 host.docker.internal \
    || sed -i "s/xdebug.discover_client_host=off/xdebug.discover_client_host=on/" /usr/local/etc/php/conf.d/xdebug.ini

COPY docker/symfony/php-xdebug /usr/local/bin/php-xdebug


# Override php.ini settings
#COPY ./docker/symfony/app_custom.ini /usr/local/etc/php/conf.d/app_custom.ini

WORKDIR /home/castor/fdp

COPY docker/symfony/init.sh /init.sh
