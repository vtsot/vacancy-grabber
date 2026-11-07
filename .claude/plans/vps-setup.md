# VPS setup: Traefik + несколько проектов

## Context
Есть VPS (Ubuntu 24.04, SSH-доступ). На нём нужно держать несколько проектов в Docker за одним reverse proxy. Первый проект — vacancy-grabber. Пока домена нет, он должен открываться по `http://<IP VPS>`. Позже появятся домены `site1.com`, `site2.com`, `sub-1.site1.com` и т.д.

Сейчас `docker-compose.prod.yaml` рассчитан на **nginx-proxy** (`VIRTUAL_HOST`). Пользователь хочет **Traefik**: сертификаты в `letsencrypt/dad.json` вместо `acme.json`. Поэтому нужно две вещи:
1. Подробная пошаговая инструкция на русском по настройке VPS.
2. Перевод prod-стека проекта на Traefik-labels.

Структура на сервере (пользователь `dad`):
```
/home/dad/workspace/
├── backups/                   # скрипты бэкапов (*.sh) + backup.log
│   └── dump/                  # архивы дампов БД (*.sql.gz), хранятся 7 дней
├── infrastructure/            # Traefik (общий reverse proxy)
│   ├── docker-compose.yaml
│   ├── .env                   # ACME_EMAIL
│   └── letsencrypt/dad.json   # сертификаты, chmod 600
└── www/
    └── vacancy-grabber/       # git clone проекта (будущие: site1.com/, sub-1.site1.com/ ...)
```

## Deliverables
1. **`docs/vps-setup.md`** — новый файл, подробная инструкция (RU) со всеми командами.
2. **`docs/infrastructure/docker-compose.yaml`** — шаблон Traefik, копируется на сервер в `/home/dad/workspace/infrastructure/`.
3. **`docker-compose.prod.yaml`** — убрать `VIRTUAL_*`/`LETSENCRYPT_HOST`, добавить Traefik-labels на `nginx`.
4. **`.env.prod.dist`** — `APP_DOMAIN` = IP по умолчанию, `DEFAULT_URI=http://<IP>/`, комментарии про переход на домен.
5. **`readme.md`** — блок с nginx-proxy заменить коротким разделом со ссылкой на `docs/vps-setup.md`.
6. После выхода из plan mode — скопировать этот план в `.claude/plans/vps-setup.md` (правило проекта).

Внутренняя сеть Traefik ↔ проекты — внешняя docker-сеть `reverse-proxy` (имя уже используется в prod compose, менять не нужно).

## 2. Traefik (`docs/infrastructure/docker-compose.yaml`)
- `traefik:v3.x` (точная версия), `restart: unless-stopped`, порты `80:80`, `443:443`.
- Provider docker: `exposedByDefault=false`, `network=reverse-proxy`.
- Entrypoints `web` (:80) и `websecure` (:443). **Глобального редиректа на https нет**: пока доступ по IP, нужен чистый HTTP. Редирект включается на роутере конкретного проекта, когда у него есть домен.
- `certificatesresolvers.le.acme.storage=/letsencrypt/dad.json`, `httpchallenge.entrypoint=web`, `email=${ACME_EMAIL}`.
- Volumes: `/var/run/docker.sock:ro` и `./letsencrypt:/letsencrypt`.
- Dashboard не публикуется наружу; при необходимости — через SSH-туннель.

## 3. Labels проекта (`docker-compose.prod.yaml`, сервис `nginx`)
Ссылка на `${APP_DOMAIN}` в окружении убирается, остаются labels:
```yaml
labels:
  - traefik.enable=true
  - traefik.docker.network=reverse-proxy
  - traefik.http.services.vacancy-grabber.loadbalancer.server.port=80
  # Этап 1: доступ по IP (HTTP)
  - traefik.http.routers.vacancy-grabber.rule=Host(`${APP_DOMAIN}`)
  - traefik.http.routers.vacancy-grabber.entrypoints=web
  # Этап 2: домен + HTTPS — раскомментировать после настройки DNS
  # - traefik.http.routers.vacancy-grabber-https.rule=Host(`${APP_DOMAIN}`)
  # - traefik.http.routers.vacancy-grabber-https.entrypoints=websecure
  # - traefik.http.routers.vacancy-grabber-https.tls.certresolver=le
  # - traefik.http.routers.vacancy-grabber.middlewares=vacancy-grabber-redirect
  # - traefik.http.middlewares.vacancy-grabber-redirect.redirectscheme.scheme=https
```
`APP_DOMAIN=<IP>` работает, потому что при заходе по IP заголовок `Host` равен IP. `framework.yaml` уже доверяет `X-Forwarded-*` из приватных сетей (`trusted_proxies`), а `cookie_secure: auto` подходит для HTTP.

## 1. Содержание `docs/vps-setup.md` (по шагам, с командами)
1. **Первый вход под root.** `apt update && apt upgrade -y`, timezone, `hostnamectl`.
2. **Пользователь `dad`.** `adduser dad`, `usermod -aG sudo dad`, перенос `authorized_keys`, проверка входа под `dad`.
3. **Защита SSH.** `/etc/ssh/sshd_config.d/99-hardening.conf`: `PermitRootLogin no`, `PasswordAuthentication no`. Перед `systemctl restart ssh` — проверить вход во втором окне.
4. **Firewall.** `ufw allow OpenSSH`, `80/tcp`, `443/tcp`, `ufw enable`. Предупреждение: Docker обходит ufw для опубликованных портов, поэтому публиковать порты (`ports:`) можно только у Traefik.
5. **fail2ban + unattended-upgrades.**
6. **Swap 2–4 ГБ.** Сборка webpack внутри Docker съедает много RAM, на маленьком VPS без swap будет OOM.
7. **Утилиты.** `apt install -y git make curl ca-certificates`.
8. **Docker Engine + compose plugin** из официального apt-репозитория Docker (команды для noble). `usermod -aG docker dad`, перелогин, `docker run hello-world`.
9. **Структура каталогов.** `mkdir -p /home/dad/workspace/{infrastructure/letsencrypt,www,backups/dump}`, `touch letsencrypt/dad.json && chmod 600`.
10. **Сеть и Traefik.** `docker network create reverse-proxy`. Положить `docker-compose.yaml` и `.env` (ACME_EMAIL) в `infrastructure/`, `docker compose up -d`, `docker compose logs`.
11. **Доступ к репозиторию.** `ssh-keygen -t ed25519` под `dad`, добавить публичный ключ как Deploy key (GitHub `vtsot/vacancy-grabber` / Bitbucket), `ssh -T git@github.com`.
12. **Клонирование.** `cd /home/dad/workspace/www && git clone <repo> vacancy-grabber`.
13. **Секреты.** `cp .env.prod.dist .env.prod.local`, `chmod 600`. Заполнить: `APP_DOMAIN=<IP>`, `APP_SECRET` (`openssl rand -hex 32`), пароли MySQL, `DATABASE_URL`, `JWT_PASSPHRASE`, `DEFAULT_URI=http://<IP>/`.
14. **Первый запуск.** `make prod-up`, `make prod-jwt-keys`, `make prod-migrate`, `make prod-ps`. Создание админа (готовой команды в проекте нет — описать вариант через `prod-console`).
15. **Проверка.** `curl -I http://<IP>/`, браузер `http://<IP>/admin/...`, `make prod-logs` (в логах worker видно обработку сообщений).
16. **Обновление проекта.** `git pull && make prod-up && make prod-migrate`, `docker image prune -f`.
17. **Бэкапы БД.** Скрипт `/home/dad/workspace/backups/backup-vacancy-grabber.sh`: `mysqldump` через `docker compose exec` → `/home/dad/workspace/backups/dump/vacancy-grabber-<дата>.sql.gz`, ротация 7 дней в `dump/`. Cron ежедневно в 03:00, лог `/home/dad/workspace/backups/backup.log`. Восстановление из `dump/`.
18. **Переход на домен.** DNS A-запись → IP. В `.env.prod.local` `APP_DOMAIN=site.com`, `DEFAULT_URI=https://...`, раскомментировать HTTPS-labels, `make prod-up`. Сертификат появится в `dad.json`.
19. **Добавление следующего проекта** (`/home/dad/workspace/www/site1.com`, `sub-1.site1.com`):
    - уникальные имена роутеров/сервисов в labels;
    - подключение к сети `reverse-proxy`;
    - без `ports:`;
    - уникальное имя compose-проекта (имя папки или `name:` в compose).
    Почему два проекта не могут одновременно жить «по IP»: на IP по Host-правилу отвечает только один проект, остальным нужны домены.
20. **Шпаргалка/troubleshooting.** 404 от Traefik (не тот Host / контейнер не в сети), 502 (nginx не видит php-fpm), лимиты Let's Encrypt.

## Files touched
- New: `docs/vps-setup.md`, `docs/infrastructure/docker-compose.yaml`, `docs/infrastructure/.env.dist`, `.claude/plans/vps-setup.md`
- Edited: `docker-compose.prod.yaml` (Traefik labels вместо `VIRTUAL_*`), `.env.prod.dist`, `readme.md`

## Verification (локально)
1. `docker compose -f docker-compose.prod.yaml --env-file <tmp env> config` проходит без ошибок, labels подставлены с IP.
2. Локально поднять Traefik из `docs/infrastructure` на свободных портах (например `8080:80`) и prod-стек с `APP_DOMAIN=localhost`. Проверить, что `curl -H 'Host: localhost' http://127.0.0.1:8080/` отдаёт страницу приложения. Если порты 80/443 заняты локальным nginx-proxy, ограничиться `config`-проверкой.
3. После проверки удалить временные контейнеры, тома и env.
