# Deploy to live

## Context
The project (Symfony 8.1 / PHP 8.4, Webpack Encore + Vue/Vuetify assets, MySQL 8, Messenger on the Doctrine transport) only has a **dev** Docker setup:
- `.docker/php-fpm/Dockerfile` installs dev tools (pcov, mc, inotify) and expects the source to be bind-mounted (`./:/var/www/html`).
- `.docker/nodejs` runs `yarn watch`. Nginx shares the code through `volumes_from`.
- Compose also runs selenium, phpmyadmin and adminer, and hard-codes the DB passwords.

To deploy to a VPS we need one **self-contained production image** with the code, the `--no-dev` vendor packages and the compiled assets inside it, plus a production compose file that runs it with no bind mounts of the source.

## Deliverables
1. `Dockerfile` (project root): the main multi-stage production Dockerfile
2. `.dockerignore`
3. `.docker/nginx/config/conf.d/prod.conf`: prod vhost (no `acceptance.*` server, real domain)
4. `docker-compose.prod.yaml`: the production stack
5. `.env.prod.dist`: a template for the server's `.env.prod.local` (holds the secrets and is never committed)
6. Makefile targets: `prod-build`, `prod-up`, `prod-migrate`, `prod-logs`
7. A "Deploy to live" section in `readme.md`

## 1. Root `Dockerfile` (multi-stage)

```dockerfile
# syntax=docker/dockerfile:1
ARG PHP_VERSION=8.4

########## 1. Composer vendor (no dev) ##########
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-progress \
      --prefer-dist --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --no-dev --classmap-authoritative

########## 2. Frontend assets ##########
FROM node:24-alpine AS assets
WORKDIR /app
COPY package.json yarn.lock ./
RUN yarn install --frozen-lockfile
# webpack.config.js reads ./.env; jsrouting-bundle JS is imported from vendor
COPY --from=vendor /app/vendor ./vendor
COPY webpack.config.js tsconfig.json .env ./
COPY assets ./assets
RUN NODE_ENV=production yarn build      # -> public/assets/

########## 3. PHP-FPM runtime (default target) ##########
FROM php:${PHP_VERSION}-fpm AS app
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN install-php-extensions zip opcache intl pdo_mysql bcmath gd exif xsl \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY .docker/php-fpm/config/php.prod.ini /usr/local/etc/php/conf.d/zz-prod.ini
COPY .docker/php-fpm/config/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf
RUN groupadd -g 1000 app && useradd -u 1000 -g app -m app

WORKDIR /var/www/html
ENV APP_ENV=prod APP_DEBUG=0
COPY --chown=app:app . .
COPY --chown=app:app --from=vendor /app/vendor ./vendor
COPY --chown=app:app --from=assets /app/public/assets ./public/assets

USER app
# bundles -> public/bundles (Sonata, Nelmio), warm the prod cache
RUN php bin/console assets:install public --no-interaction \
 && php bin/console cache:clear --no-warmup \
 && php bin/console cache:warmup

CMD ["php-fpm"]

########## 4. Nginx serving the static files ##########
FROM nginx:mainline-alpine AS nginx
COPY .docker/nginx/config/nginx.conf /etc/nginx/nginx.conf
COPY .docker/nginx/config/conf.d/prod.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/html/public /var/www/html/public
```

Notes:
- **One Dockerfile, two targets.** `app` is the PHP code (used for both php-fpm and the Messenger worker). `nginx` holds a copy of `public/` (assets and bundles) and passes `index.php` to `php-fpm:9000`. The `volumes_from` sharing is gone.
- **Dev-only extensions are dropped:** pcov, inotify, mc, amqp, redis, excimer and pdo_pgsql. Redis was already removed (commit a66b2a1), and Messenger uses Doctrine. During implementation, run `grep` for `amqp`/`excimer` to confirm they are unused.
- **New `.docker/php-fpm/config/php.prod.ini`:** `opcache.validate_timestamps=0`, `opcache.preload` is optional, `display_errors=Off`, `expose_php=Off`.
- **Cache warmup at build time** needs no DB. If any service connects during warmup, pass a dummy `DATABASE_URL` as a build ENV in that `RUN`.
- **During implementation, check `assets/` for imports from `vendor/friendsofsymfony/jsrouting-bundle`.** If none exist, remove the `COPY --from=vendor` line from the assets stage.

## 2. `.dockerignore`
```
.git
.idea
.claude
node_modules
vendor
var
public/assets
public/bundles
.docker/mysql/data
.docker/mysql/db-dump
tests
docs
.env.local
.env.*.local
.php-cs-fixer.cache
docker-compose*.yaml
```
(`.docker/mysql/data` holds the large local DB. It must stay out of the build context.)

## 3. `docker-compose.prod.yaml`
Services, all on `restart: unless-stopped`:
- **php-fpm**: `build: {context: ., target: app}`, image `vacancy-grabber-app:latest`. Uses `env_file: .env.prod.local`. Volumes `jwt:/var/www/html/config/jwt` and `uploads:/var/www/html/public/uploaded`. Joins the `backend` and `shared-smtp` networks (only if SMTP is used on the VPS).
- **worker**: same image, `command: php bin/console messenger:consume queue_high_priority queue_standard_priority queue_low_priority --time-limit=3600 --memory-limit=256M`. It restarts itself after `time-limit`.
- **nginx**: `build: {target: nginx}`. Joins the `reverse-proxy` network with `VIRTUAL_HOST=<domain>`, `LETSENCRYPT_HOST=<domain>` and `VIRTUAL_PORT=80`, and the `backend` network.
- **mysql**: reuse `.docker/mysql` (image `mysql:8.0`). The credentials come from env vars, not hard-coded values. Named volume `mysql-data`. **No published ports.**
- Not included: selenium, phpmyadmin, adminer and nodejs.

TLS: this keeps the `nginx-proxy` + `VIRTUAL_HOST` approach the project already uses locally. On the VPS, run `nginxproxy/nginx-proxy` together with `nginxproxy/acme-companion` on the external `reverse-proxy` network to get Let's Encrypt certificates.

## 4. Secrets/env on the server
`.env.prod.dist`, copied to `.env.prod.local` on the VPS:
`APP_SECRET`, `DATABASE_URL=mysql://user:pass@mysql:3306/vacancy_grabber?serverVersion=8.0.32&charset=utf8mb4`, `MYSQL_*`, `DEFAULT_URI=https://<domain>/`, `CORS_ALLOW_ORIGIN`, `JWT_PASSPHRASE`, `MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0`.
The JWT keys are generated once into the `jwt` volume: `docker compose -f docker-compose.prod.yaml exec php-fpm bin/console lexik:jwt:generate-keypair`.

## 5. Deploy procedure (readme + makefile)
1. VPS: install Docker and the compose plugin, create the networks (`docker network create reverse-proxy`), and start nginx-proxy and acme-companion.
2. Point the domain's DNS A record at the VPS.
3. `git clone`, then `cp .env.prod.dist .env.prod.local` and fill in the secrets.
4. `make prod-up`, which runs `docker compose -f docker-compose.prod.yaml up -d --build`.
5. `make prod-migrate`, which runs `exec php-fpm bin/console doctrine:migrations:migrate --no-interaction`. Then run `messenger:setup-transports` (because of `auto_setup=0`).
6. First time only: generate the JWT keypair and create an admin user.
7. Update: `git pull && make prod-up && make prod-migrate`.
8. Optional: import the existing DB from `.docker/mysql/db-dump`.

## Files touched
- New: `Dockerfile`, `.dockerignore`, `docker-compose.prod.yaml`, `.env.prod.dist`, `.docker/nginx/config/conf.d/prod.conf`, `.docker/php-fpm/config/php.prod.ini`
- Edited: `makefile` (prod targets), `readme.md` (deploy section)
- The dev setup (`docker-compose.yaml`, `.docker/*/Dockerfile`) is not changed.

## Verification (locally before the VPS)
1. `docker build --target app -t vg-app .` and `docker build --target nginx -t vg-nginx .` both succeed.
2. `docker run --rm vg-app php bin/console about` shows `Environment: prod` and `Debug: false`.
3. `docker run --rm vg-app ls public/assets public/bundles` shows that the assets and bundles exist.
4. `docker compose -f docker-compose.prod.yaml up -d` with a test `.env.prod.local`, then run the migrations. Open the site through the proxy and check that `/admin/source/list` loads with CSS/JS.
5. Click "Grab" on a source and confirm that the `worker` logs show the message being consumed.
6. `docker compose -f docker-compose.prod.yaml ps`: all containers are healthy, and mysql exposes no port to the host.
