# syntax=docker/dockerfile:1
#
# Production image for Vacancy Grabber.
#
# Targets:
#   app   - PHP-FPM with code, prod vendor and compiled assets (also used for the messenger worker)
#   nginx - Nginx serving public/ and proxying PHP requests to the "php-fpm" service
#
# Build:
#   docker build --target app   -t vacancy-grabber-app .
#   docker build --target nginx -t vacancy-grabber-nginx .

ARG PHP_VERSION=8.4

########## Base PHP runtime ##########
FROM php:${PHP_VERSION}-fpm AS php-base

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions zip opcache intl pdo_mysql bcmath gd exif xsl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/*

COPY .docker/php-fpm/config/php.ini /usr/local/etc/php/php.ini
COPY .docker/php-fpm/config/php.prod.ini /usr/local/etc/php/conf.d/zz-prod.ini
COPY .docker/php-fpm/config/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf

RUN groupadd -g 1000 app \
    && useradd -u 1000 -g app -m -s /bin/bash app \
    && chown app:app /var/www/html

WORKDIR /var/www/html

########## Composer vendor (no dev) ##########
FROM php-base AS vendor

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-progress --prefer-dist --no-interaction

COPY . .
RUN composer dump-autoload --no-dev --classmap-authoritative --no-interaction

########## Frontend assets (Webpack Encore) ##########
FROM node:24-alpine AS assets

WORKDIR /app

COPY package.json yarn.lock ./
RUN yarn install --frozen-lockfile --non-interactive

# assets/js/module/RouteGenerator.ts imports FOSJsRoutingBundle JS from vendor/
COPY --from=vendor /var/www/html/vendor/friendsofsymfony ./vendor/friendsofsymfony
# webpack.config.js reads ./.env
COPY webpack.config.js tsconfig.json .env ./
COPY assets ./assets

# TS_TRANSPILE_ONLY: compile without type-checking (see webpack.config.js)
RUN NODE_ENV=production TS_TRANSPILE_ONLY=1 yarn build

########## Application (PHP-FPM) ##########
FROM php-base AS app

ENV APP_ENV=prod \
    APP_DEBUG=0

COPY --chown=app:app . .
COPY --chown=app:app --from=vendor /var/www/html/vendor ./vendor
COPY --chown=app:app --from=assets /app/public/assets ./public/assets

USER app:app

RUN mkdir -p var/cache var/log config/jwt \
    && php bin/console assets:install public --no-interaction \
    && php bin/console cache:clear --no-warmup --no-interaction \
    && php bin/console cache:warmup --no-interaction

EXPOSE 9000
CMD ["php-fpm"]

########## Nginx ##########
FROM nginx:mainline-alpine AS nginx

COPY .docker/nginx/config/nginx.conf /etc/nginx/nginx.conf
COPY .docker/nginx/config/conf.d/prod.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/html/public /var/www/html/public

WORKDIR /var/www/html
EXPOSE 80
