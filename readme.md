# Vacancy grabber
Grab vacancies by source URL

## Requirements:
- php 8.4
- mysql 8

## Installation:

### Add a host 
edit /etc/hosts  
add a new host: vacancy-grabber.local

### Compile all docker images

#### Build docker images
```bash
make docker-build
```

#### Run docker images
```bash
make docker-up
```

#### Run docker logs
```bash
make docker-logs
```

#### Connect to php-fpm console
```bash
make php-console
```

#### Connect to mysql console
```bash
make mysql-console
```

[//]: # (#### Connect to nodejs console)
[//]: # (```bash)
[//]: # (make nodejs-console)
[//]: # (```)

#### Install vendor packages
```bash
make composer-install
```

#### Run Migrations
```bash
make migrate
```

## Code Style:

### PHP
> **_NOTE:_** All commands must be executed inside docker image

#### Check
```bash
composer run lint
```
#### Fix
```bash
composer run lint:fix
```

## Start using:
### Sources
- Open [Vacancies ⇒ Sources](https://vacancy-grabber.local/admin/source/list) left menu 
- Update URL for the required sources 
- Click on the "Grab" action button for the chosen sources
  ![Vacancies ⇒ Sources list](./docs/images/admin-source-list.png)

## Deploy to live (VPS)

The production image is built from the root `Dockerfile` (targets `app` and `nginx`). The stack lives in `docker-compose.prod.yaml`: php-fpm, the messenger worker, nginx and mysql. It runs behind a shared Traefik reverse proxy.

Full step-by-step server setup (Ubuntu 24.04, Docker, Traefik, first deploy, backups, domains, more projects): **[vps-setup/docs/vps-setup.md](../vps-setup/docs/vps-setup.md)**.

### Short version (server already prepared)
```bash
cd /home/dad/workspace/www/vacancy-grabber
cp .env.prod.dist .env.prod.local   # fill in real values
make prod-up        # build images and start the stack
make prod-jwt-keys  # one-time: JWT keypair (stored in the "jwt" volume)
make prod-migrate   # DB migrations + messenger transports
```

### Update
```bash
git pull && make prod-up && make prod-migrate
```
