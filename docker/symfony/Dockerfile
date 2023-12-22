FROM php:8.2-fpm-alpine

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install DB driver for PHP => pdo pdo_mysql
RUN set -xe \
    && apk add --no-cache --update --virtual .phpize-deps $PHPIZE_DEPS \
    && apk add bash git \
    && apk add linux-headers \
    && pecl install xdebug-3.3.1 \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install pdo pdo_mysql \
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
