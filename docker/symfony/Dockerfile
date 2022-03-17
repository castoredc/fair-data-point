FROM php:7.4-fpm-alpine

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install DB driver for PHP => pdo pdo_mysql
RUN set -xe \
    && apk add --no-cache --update --virtual .phpize-deps $PHPIZE_DEPS \
    && apk add git \
    && docker-php-ext-install pdo pdo_mysql \
    && rm -rf /usr/share/php \
    && rm -rf /tmp/* \
    && apk del  .phpize-deps \
    && apk add bind-tools

# Override php.ini settings
#COPY ./docker/symfony/app_custom.ini /usr/local/etc/php/conf.d/app_custom.ini

WORKDIR /var/www/html

COPY . /var/www/html

# To run some additional command during build time (db migrations, loading fixtures, etc)
COPY ./docker/symfony/prepare.sh .
RUN chmod a+x ./prepare.sh

# Install composer
RUN composer install -v