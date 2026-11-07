# Настройка VPS (Ubuntu 24.04) для нескольких проектов

Это пошаговая инструкция: чистый VPS → Docker → общий reverse proxy **Traefik** → проект **vacancy-grabber**. Проект будет открываться по `http://<IP VPS>`. Позже на этот же сервер можно добавлять другие проекты со своими доменами.

## Как это устроено

```
Интернет ──► :80 / :443 ──► Traefik (infrastructure)
                               │   сеть docker "reverse-proxy"
                               ├──► vacancy-grabber: nginx ─► php-fpm / worker ─► mysql
                               ├──► site1.com        (позже)
                               └──► sub-1.site1.com  (позже)
```

- Наружу открыты только порты 80 и 443, и слушает их только Traefik.
- Каждый проект — отдельный `docker compose` в своей папке. С Traefik он связан через сеть `reverse-proxy` и labels.
- Traefik сам получает сертификаты Let's Encrypt для доменов и хранит их в `letsencrypt/dad.json`.

### Структура каталогов на сервере

```
/home/dad/workspace/
├── infrastructure/                 # Traefik
│   ├── docker-compose.yaml         # копия docs/infrastructure/docker-compose.yaml
│   ├── .env                        # ACME_EMAIL
│   └── letsencrypt/
│       └── dad.json                # сертификаты (chmod 600)
└── www/
    └── vacancy-grabber/            # git clone проекта
    # позже: site1.com/  site2.com/  sub-1.site1.com/ ...
```

В примерах ниже:
- `100.91.57.78` — IP вашего VPS (замените на свой);
- `dad` — пользователь на сервере.

---

## Шаг 1. Первый вход и обновление системы

С локального компьютера:

```bash
ssh root@100.91.57.78
```

На сервере:

```bash
apt update && apt upgrade -y
timedatectl set-timezone UTC          # или Europe/Kyiv
hostnamectl set-hostname vps-main
reboot                                # если обновилось ядро
```

## Шаг 2. Пользователь `dad`

Работать под root небезопасно, поэтому создаём обычного пользователя с sudo:

```bash
adduser dad                           # задать пароль (нужен для sudo)
usermod -aG sudo dad

# скопировать SSH-ключ root'а пользователю dad
mkdir -p /home/dad/.ssh
cp /root/.ssh/authorized_keys /home/dad/.ssh/authorized_keys
chown -R dad:dad /home/dad/.ssh
chmod 700 /home/dad/.ssh
chmod 600 /home/dad/.ssh/authorized_keys
```

> Если вы входите на сервер по паролю и ключа ещё нет, на **локальном** компьютере выполните:
> `ssh-keygen -t ed25519` (если ключа нет), затем `ssh-copy-id dad@100.91.57.78`.

**Не закрывая текущую сессию**, проверьте вход в новом окне терминала:

```bash
ssh dad@100.91.57.78
sudo whoami                           # должно вывести: root
```

## Шаг 3. Защита SSH

Делайте это только после того, как вход под `dad` по ключу заработал.

```bash
sudo tee /etc/ssh/sshd_config.d/99-hardening.conf > /dev/null <<'EOF'
PermitRootLogin no
PasswordAuthentication no
KbdInteractiveAuthentication no
PubkeyAuthentication yes
EOF

sudo sshd -t && sudo systemctl restart ssh
```

Снова проверьте вход `ssh dad@100.91.57.78` в **новом** окне. Только после этого закрывайте старую сессию.

## Шаг 4. Firewall (ufw)

```bash
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow OpenSSH
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
sudo ufw status verbose
```

> ⚠️ Docker обходит правила ufw для опубликованных портов (`ports:` в compose). Поэтому `ports:` есть **только у Traefik**. У проектов используйте `expose:` и сеть `reverse-proxy`. MySQL наружу не публикуется.

## Шаг 5. fail2ban и автообновления безопасности

```bash
sudo apt install -y fail2ban unattended-upgrades
sudo systemctl enable --now fail2ban
sudo dpkg-reconfigure -plow unattended-upgrades   # ответить "Yes"
sudo fail2ban-client status sshd
```

## Шаг 6. Swap

Сборка фронтенда (webpack) внутри Docker требует много памяти. На VPS с 1–4 ГБ RAM без swap сборка упадёт с ошибкой OOM.

```bash
free -h                                # проверить, есть ли уже swap
sudo fallocate -l 4G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
echo 'vm.swappiness=10' | sudo tee /etc/sysctl.d/99-swap.conf
sudo sysctl --system
free -h
```

## Шаг 7. Базовые утилиты

```bash
sudo apt install -y git make curl ca-certificates gnupg htop
```

## Шаг 8. Docker Engine + Docker Compose

Ставим из официального репозитория Docker. Пакет `docker.io` из репозитория Ubuntu не подходит: он устаревший.

```bash
# удалить возможные старые пакеты
for pkg in docker.io docker-doc docker-compose docker-compose-v2 podman-docker containerd runc; do
  sudo apt remove -y $pkg 2>/dev/null
done

# ключ и репозиторий Docker
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc

echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] \
https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" \
  | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# запуск docker без sudo
sudo usermod -aG docker dad
```

Чтобы группа `docker` применилась, выйдите и войдите снова:

```bash
exit
ssh dad@100.91.57.78

docker version
docker compose version
docker run --rm hello-world
```

Ограничим размер логов контейнеров, иначе со временем они займут весь диск:

```bash
sudo tee /etc/docker/daemon.json > /dev/null <<'EOF'
{
  "log-driver": "json-file",
  "log-opts": { "max-size": "20m", "max-file": "5" }
}
EOF
sudo systemctl restart docker
```

## Шаг 9. Структура каталогов

```bash
mkdir -p /home/dad/workspace/infrastructure/letsencrypt
mkdir -p /home/dad/workspace/www
mkdir -p /home/dad/backups

touch /home/dad/workspace/infrastructure/letsencrypt/dad.json
chmod 600 /home/dad/workspace/infrastructure/letsencrypt/dad.json
```

> Права `600` на `dad.json` обязательны. Иначе Traefik откажется работать с сертификатами.

## Шаг 10. Общая сеть и Traefik

Сеть создаётся один раз. Через неё Traefik видит все проекты:

```bash
docker network create reverse-proxy
```

Создайте `docker-compose.yaml` для Traefik. Его содержимое лежит в проекте: [`docs/infrastructure/docker-compose.yaml`](infrastructure/docker-compose.yaml). Скопировать его можно двумя способами:
- после шага 12 (клонирование проекта): `cp /home/dad/workspace/www/vacancy-grabber/docs/infrastructure/docker-compose.yaml /home/dad/workspace/infrastructure/`;
- или сразу, через `nano /home/dad/workspace/infrastructure/docker-compose.yaml` и вставку содержимого.

Email для Let's Encrypt:

```bash
cd /home/dad/workspace/infrastructure
echo 'ACME_EMAIL=you@example.com' > .env
```

Запуск:

```bash
cd /home/dad/workspace/infrastructure
docker compose up -d
docker compose ps
docker compose logs -f traefik           # Ctrl+C для выхода
```

Проверка: `curl -I http://100.91.57.78` должен вернуть `404 page not found`. Это нормально: Traefik работает, просто роутов ещё нет.

Dashboard Traefik наружу не открыт. Посмотреть его можно через SSH-туннель:

```bash
# на локальном компьютере
ssh -L 8080:127.0.0.1:8080 dad@100.91.57.78
# затем открыть в браузере http://localhost:8080
```

## Шаг 11. Доступ сервера к git-репозиторию

Создаём на сервере отдельный ключ и добавляем его в репозиторий как **Deploy key** (только чтение):

```bash
ssh-keygen -t ed25519 -C "dad@vps-main" -f ~/.ssh/id_ed25519 -N ""
cat ~/.ssh/id_ed25519.pub
```

Куда добавить ключ:
- **GitHub:** репозиторий → *Settings → Deploy keys → Add deploy key* (галочку write не ставить);
- **Bitbucket:** репозиторий → *Repository settings → Access keys → Add key*.

Проверка:

```bash
ssh -T git@github.com            # или: ssh -T git@bitbucket.org
```

## Шаг 12. Клонирование проекта

```bash
cd /home/dad/workspace/www
git clone git@github.com:vtsot/vacancy-grabber.git vacancy-grabber
# или: git clone git@bitbucket.org:vitalii-sotnykov/vacancy-grabber.git vacancy-grabber
cd /home/dad/workspace/www/vacancy-grabber
```

> Имя папки `vacancy-grabber` становится именем compose-проекта: оно попадает в имена контейнеров и томов. Не переименовывайте папку после первого запуска, иначе Docker создаст новые пустые тома для БД.

## Шаг 13. Секреты проекта

```bash
cd /home/dad/workspace/www/vacancy-grabber
cp .env.prod.dist .env.prod.local
chmod 600 .env.prod.local

# сгенерировать значения
openssl rand -hex 32        # → APP_SECRET
openssl rand -base64 24     # → MYSQL_PASSWORD
openssl rand -base64 24     # → MYSQL_ROOT_PASSWORD
openssl rand -base64 24     # → JWT_PASSPHRASE

nano .env.prod.local
```

Что заполнить:

| Переменная | Значение |
|---|---|
| `APP_DOMAIN` | IP сервера, например `100.91.57.78` |
| `APP_SECRET` | результат `openssl rand -hex 32` |
| `DEFAULT_URI` | `http://100.91.57.78/` |
| `CORS_ALLOW_ORIGIN` | `'^https?://203\.0\.113\.10$'` |
| `MYSQL_DATABASE` / `MYSQL_USER` | например `vacancy_grabber` / `vacancy_grabber` |
| `MYSQL_PASSWORD` / `MYSQL_ROOT_PASSWORD` | сгенерированные пароли |
| `DATABASE_URL` | `mysql://vacancy_grabber:<MYSQL_PASSWORD>@mysql:3306/vacancy_grabber?serverVersion=8.0.32&charset=utf8mb4` |
| `JWT_PASSPHRASE` | сгенерированная строка |

> Если в пароле есть символы `@ : / ? # %`, в `DATABASE_URL` их нужно URL-кодировать. Проще генерировать пароли без них: `openssl rand -hex 24`.

## Шаг 14. Первый запуск проекта

```bash
cd /home/dad/workspace/www/vacancy-grabber

make prod-up         # сборка образов и запуск (первая сборка 5–15 минут)
make prod-ps         # все контейнеры Up, mysql — healthy
make prod-jwt-keys   # один раз: JWT-ключи в docker-томе "jwt"
make prod-migrate    # миграции БД + таблицы очередей messenger
```

Если `make` недоступен, те же команды без него:

```bash
docker compose -f docker-compose.prod.yaml --env-file .env.prod.local up -d --build
docker compose -f docker-compose.prod.yaml --env-file .env.prod.local exec php-fpm php bin/console doctrine:migrations:migrate --no-interaction
```

**Администратор.** Готовой консольной команды для создания пользователя в проекте нет. Варианты:
- перенести БД с локальной машины (см. шаг 17, «Восстановление»);
- добавить пользователя напрямую в таблицу пользователей через MySQL-консоль:
  ```bash
  docker compose -f docker-compose.prod.yaml --env-file .env.prod.local exec mysql \
    mysql -u root -p vacancy_grabber
  ```
  Хеш пароля можно получить так: `make prod-console` → `php bin/console security:hash-password`.

## Шаг 15. Проверка

```bash
curl -I http://100.91.57.78/           # 200 или 302 (редирект на логин)
make prod-logs                          # логи всех контейнеров
```

В браузере: `http://100.91.57.78/admin/source/list`. Страница должна открыться со стилями.
Нажмите «Grab» у источника. В `make prod-logs` у контейнера `worker` должна появиться обработка сообщения.

## Шаг 16. Обновление проекта

```bash
cd /home/dad/workspace/www/vacancy-grabber
git pull
make prod-up
make prod-migrate
docker image prune -f                  # удалить старые образы
```

## Шаг 17. Бэкапы БД

Скрипт `/home/dad/backups/backup-vacancy-grabber.sh`:

```bash
cat > /home/dad/backups/backup-vacancy-grabber.sh <<'EOF'
#!/usr/bin/env bash
set -euo pipefail
cd /home/dad/workspace/www/vacancy-grabber
FILE=/home/dad/backups/vacancy-grabber-$(date +%F_%H%M).sql.gz
docker compose -f docker-compose.prod.yaml --env-file .env.prod.local exec -T mysql \
  sh -c 'exec mysqldump --single-transaction --routines -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' \
  | gzip > "$FILE"
find /home/dad/backups -name 'vacancy-grabber-*.sql.gz' -mtime +7 -delete
EOF
chmod 700 /home/dad/backups/backup-vacancy-grabber.sh
/home/dad/backups/backup-vacancy-grabber.sh && ls -lh /home/dad/backups
```

Cron (каждый день в 03:00):

```bash
( crontab -l 2>/dev/null; echo '0 3 * * * /home/dad/backups/backup-vacancy-grabber.sh >> /home/dad/backups/backup.log 2>&1' ) | crontab -
crontab -l
```

**Восстановление / перенос локальной БД на сервер:**

```bash
# локально: сделать дамп и скопировать на сервер
scp dump.sql.gz dad@100.91.57.78:/home/dad/backups/

# на сервере
cd /home/dad/workspace/www/vacancy-grabber
gunzip -c /home/dad/backups/dump.sql.gz | docker compose -f docker-compose.prod.yaml --env-file .env.prod.local \
  exec -T mysql sh -c 'exec mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"'
make prod-migrate
```

> Бэкапы лучше копировать и за пределы сервера (rsync / rclone в облако).

## Шаг 18. Переход с IP на домен (HTTPS)

1. У регистратора домена создайте DNS-запись **A**: `vacancy-grabber.example.com → 100.91.57.78`. Проверка: `dig +short vacancy-grabber.example.com`.
2. В `.env.prod.local`:
   ```
   APP_DOMAIN=vacancy-grabber.example.com
   DEFAULT_URI=https://vacancy-grabber.example.com/
   CORS_ALLOW_ORIGIN='^https://vacancy-grabber\.example\.com$'
   ```
3. В `docker-compose.prod.yaml` раскомментируйте блок labels «Stage 2» у сервиса `nginx`.
4. Примените изменения:
   ```bash
   make prod-up
   docker logs -f traefik            # получение сертификата
   ```
5. Откройте `https://vacancy-grabber.example.com`. HTTP будет перенаправлять на HTTPS. Сертификат сохранится в `/home/dad/workspace/infrastructure/letsencrypt/dad.json` и будет продлеваться автоматически.

> После перехода на домен проект перестанет открываться по IP. Так и задумано: IP освобождается.

## Шаг 19. Добавление следующего проекта

Пример: `site1.com` в папке `/home/dad/workspace/www/site1.com`.

```bash
cd /home/dad/workspace/www
git clone <repo> site1.com
```

В его `docker-compose` у веб-сервиса (nginx / приложение):

```yaml
services:
    web:
        # ... image/build ...
        expose:
            - "80"
        labels:
            - traefik.enable=true
            - traefik.docker.network=reverse-proxy
            - traefik.http.services.site1.loadbalancer.server.port=80
            - traefik.http.routers.site1.rule=Host(`site1.com`) || Host(`www.site1.com`)
            - traefik.http.routers.site1.entrypoints=web
            - traefik.http.routers.site1.middlewares=site1-redirect
            - traefik.http.middlewares.site1-redirect.redirectscheme.scheme=https
            - traefik.http.routers.site1-https.rule=Host(`site1.com`) || Host(`www.site1.com`)
            - traefik.http.routers.site1-https.entrypoints=websecure
            - traefik.http.routers.site1-https.tls.certresolver=le
            - traefik.http.routers.site1-https.service=site1
        networks:
            - reverse-proxy
            - default

networks:
    reverse-proxy:
        external: true
```

Для поддомена `sub-1.site1.com` (папка `/home/dad/workspace/www/sub-1.site1.com`) всё так же, с правилом ``Host(`sub-1.site1.com`)`` и своими именами роутеров.

Правила для каждого нового проекта:
- **Уникальные имена** роутеров, сервисов и middleware в labels (`site1`, `sub1-site1`, …). Одинаковые имена в двух проектах конфликтуют.
- **Никаких `ports:`.** Только `expose:` и сеть `reverse-proxy`.
- **БД каждого проекта — в его внутренней сети.** К `reverse-proxy` подключается только веб-контейнер.
- **Имя папки = имя compose-проекта.** Папки должны различаться, и это уже так, потому что у каждого домена своя папка.
- **По IP отвечает только один проект.** Правилу ``Host(`<IP>`)`` соответствует один роутер, поэтому всем остальным проектам нужны домены (или поддомены).
- Перед запуском проверьте, что DNS домена уже указывает на сервер. Иначе Let's Encrypt не выдаст сертификат, а после нескольких неудачных попыток временно заблокирует запросы.

## Шаг 20. Шпаргалка и решение проблем

```bash
# Traefik
cd /home/dad/workspace/infrastructure && docker compose logs -f traefik
docker compose restart traefik

# проект
cd /home/dad/workspace/www/vacancy-grabber
make prod-ps | make prod-logs | make prod-console | make prod-down

# ресурсы
docker stats
df -h && docker system df
```

| Симптом | Причина / решение |
|---|---|
| `404 page not found` от Traefik | `Host` запроса не совпадает с `APP_DOMAIN`, или контейнер nginx не в сети `reverse-proxy`. Проверка: `docker inspect <container>` → Labels / Networks; dashboard Traefik. |
| `502 Bad Gateway` | nginx не достучался до php-fpm: `make prod-ps`, `make prod-logs`. |
| `Gateway Timeout` | Traefik и nginx в разных сетях. Проверьте label `traefik.docker.network=reverse-proxy`. |
| Сертификат не выдаётся | DNS ещё не указывает на VPS или закрыт порт 80. Права на `dad.json` должны быть `600`. Смотрите `docker logs traefik`. |
| Сборка падает: `Killed` / exit 137 | Не хватает памяти, добавьте swap (шаг 6). |
| Сайт без стилей | Образ nginx собран из старого кода: `make prod-up` (с `--build`). |
| `client version ... is too old` в логах Traefik | Старый Traefik не дружит с новым Docker. Обновите образ: `docker compose pull && docker compose up -d`. |
