## Requirements:
- php 8.4
- mysql 8

[//]: # (- openssl &#40;for JWT key generation&#41;)
[//]: # ()
[//]: # (## Installation:)

[//]: # ()
[//]: # (#### Compile all docker images)

[//]: # ()
[//]: # (> **_NOTE:_** Make sure to specify correct values in .docker/.env file)

[//]: # ()
[//]: # (```bash)

[//]: # (make docker-build)

[//]: # (```)

[//]: # ()
[//]: # (#### Run docker images)

[//]: # (```bash)

[//]: # (make docker-up)

[//]: # (```)

[//]: # ()
[//]: # (#### Connect to php-fpm console)

[//]: # (```bash)

[//]: # (make php-console)

[//]: # (```)

[//]: # ()
[//]: # (#### Connect to postgres console)

[//]: # (```bash)

[//]: # (make postgres-console)

[//]: # (```)

[//]: # ()
[//]: # (#### Load fixtures)

[//]: # (```bash)

[//]: # (make fixtures)

[//]: # (```)

[//]: # ()
[//]: # (> **_NOTE:_** All commands must be executed inside docker php console !!!)

[//]: # ()
[//]: # (#### Install vendor packages)

[//]: # (```bash)

[//]: # (composer install)

[//]: # (```)

[//]: # ()
[//]: # (#### Generate keys for JWT generator)

[//]: # (```bash)

[//]: # (php bin/console lexik:jwt:generate-keypair)

[//]: # ( ```)

[//]: # ()
[//]: # (#### Postgres requirements)

[//]: # (!!! _NOTE:_ Make sure to enable postgis extension in database before running migrations)

[//]: # ()
[//]: # (```sql)

[//]: # (-- Enable postgis extension in pgsql)

[//]: # (CREATE EXTENSION postgis;)

[//]: # (```)

[//]: # ()
[//]: # (#### Run Migrations)

[//]: # (```bash)

[//]: # (php bin/console d:m:m)

[//]: # (```)

[//]: # ()
[//]: # (#### Import Translations)

[//]: # (```bash)

[//]: # (php bin/console app:translation:import ./var/translations.yaml)

[//]: # (php bin/console app:cache:translation:clear)

[//]: # (```)

[//]: # ()
[//]: # (#### Create user example)

[//]: # (```bash)

[//]: # (php bin/console app:user:create test@test.com 123456 ROLE_ADMIN)

[//]: # (```)

[//]: # ()
[//]: # (## Code Style:)

[//]: # (### PHP)

[//]: # (#### Check)

[//]: # (```bash)

[//]: # (composer lint)

[//]: # (```)

[//]: # (#### Fix)

[//]: # (```bash)

[//]: # (composer lint:fix)

[//]: # (```)

[//]: # ()
[//]: # (#### Analyze)

[//]: # (```bash)

[//]: # (composer analyze)

[//]: # (```)

[//]: # ()
[//]: # (## Test:)

[//]: # (#### Update database)

[//]: # (```bash)

[//]: # (php bin/console d:m:m --env=test)

[//]: # (php bin/console d:f:l --env=test)

[//]: # (```)

[//]: # (#### Run)

[//]: # (```bash)

[//]: # (# test without coverage report)

[//]: # (composer test)

[//]: # (```)

[//]: # ()
[//]: # (```bash)

[//]: # (# test and generates html coverage report)

[//]: # (composer test:coverage-html)

[//]: # (```)
