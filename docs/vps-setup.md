# VPS setup (Ubuntu 24.04) for multiple projects

A step-by-step guide: clean VPS → Docker → shared **Traefik** reverse proxy → the **vacancy-grabber** project. The project will be available at `http://<VPS IP>`. Later you can add more projects with their own domains to the same server.

## How it works

```
Internet ──► :80 / :443 ──► Traefik (infrastructure)
                               │   docker network "reverse-proxy"
                               ├──► vacancy-grabber: nginx ─► php-fpm / worker ─► mysql
                               ├──► site1.com        (later)
                               └──► sub-1.site1.com  (later)
```

- Only ports 80 and 443 are open to the outside, and only Traefik listens on them.
- Each project is a separate `docker compose` stack in its own folder. It is connected to Traefik through the `reverse-proxy` network and labels.
- Traefik obtains Let's Encrypt certificates for domains on its own and stores them in `letsencrypt/dad.json`.

### Directory structure on the server

```
/home/dad/workspace/
├── infrastructure/                 # Traefik
│   ├── docker-compose.yaml         # copy of docs/infrastructure/docker-compose.yaml
│   ├── .env                        # ACME_EMAIL
│   └── letsencrypt/
│       └── dad.json                # certificates (chmod 600)
└── www/
    └── vacancy-grabber/            # git clone of the project
    # later: site1.com/  site2.com/  sub-1.site1.com/ ...
```

In the examples below:
- `100.91.57.78` is the IP of your VPS (replace it with yours);
- `dad` is the user on the server.

---

## Step 1. First login and system update

From your local computer:

```bash
ssh dad@100.91.57.78
```

On the server:

```bash
apt update && apt upgrade -y
timedatectl set-timezone UTC          # or Europe/Kyiv
hostnamectl set-hostname vps-main
reboot                                # if the kernel was updated
```

## Step 2. The `dad` user

Working as root is unsafe, so create a regular user with sudo:

```bash
adduser dad                           # set a password (needed for sudo)
usermod -aG sudo dad

# copy root's SSH key to the dad user
mkdir -p /home/dad/.ssh
cp /root/.ssh/authorized_keys /home/dad/.ssh/authorized_keys
chown -R dad:dad /home/dad/.ssh
chmod 700 /home/dad/.ssh
chmod 600 /home/dad/.ssh/authorized_keys
```

> If you log in to the server with a password and have no key yet, run this on your **local** computer:
> `ssh-keygen -t ed25519` (if you have no key), then `ssh-copy-id dad@100.91.57.78`.

**Without closing the current session**, check the login in a new terminal window:

```bash
ssh dad@100.91.57.78
sudo whoami                           # should print: root
```

## Step 3. SSH hardening

Do this only after key-based login as `dad` works.

```bash
sudo tee /etc/ssh/sshd_config.d/99-hardening.conf > /dev/null <<'EOF'
PermitRootLogin no
PasswordAuthentication no
KbdInteractiveAuthentication no
PubkeyAuthentication yes
EOF

sudo sshd -t && sudo systemctl restart ssh
```

Check the login `ssh dad@100.91.57.78` again in a **new** window. Only then close the old session.

## Step 4. Firewall (ufw)

```bash
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow OpenSSH
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
sudo ufw status verbose
```

> ⚠️ Docker bypasses ufw rules for published ports (`ports:` in compose). That is why **only Traefik** has `ports:`. Projects use `expose:` and the `reverse-proxy` network. MySQL is not published to the outside.

## Step 5. fail2ban and automatic security updates

```bash
sudo apt install -y fail2ban unattended-upgrades
sudo systemctl enable --now fail2ban
sudo dpkg-reconfigure -plow unattended-upgrades   # answer "Yes"
sudo fail2ban-client status sshd
```

## Step 6. Swap

Building the frontend (webpack) inside Docker needs a lot of memory. On a VPS with 1–4 GB of RAM and no swap, the build fails with an OOM error.

```bash
free -h                                # check whether swap already exists
sudo fallocate -l 4G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
echo 'vm.swappiness=10' | sudo tee /etc/sysctl.d/99-swap.conf
sudo sysctl --system
free -h
```

## Step 7. Basic utilities

```bash
sudo apt install -y \
    curl \
    wget \
    git \
    make \
    nano \
    vim \
    unzip \
    zip \
    htop \
    ncdu \
    ca-certificates \
    gnupg \
    lsb-release \
    software-properties-common \
    ufw \
    fail2ban \
    htop \
    mc \
    apache2-utils
```

## Step 8. Docker Engine + Docker Compose

Install from the official Docker repository. The `docker.io` package from the Ubuntu repository is outdated and not suitable.

```bash
# remove possible old packages
for pkg in docker.io docker-doc docker-compose docker-compose-v2 podman-docker containerd runc; do
  sudo apt remove -y $pkg 2>/dev/null
done

# Docker key and repository
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc

#echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] \
#https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" \
#  | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

sudo tee /etc/apt/sources.list.d/docker.sources <<EOF
Types: deb
URIs: https://download.docker.com/linux/ubuntu
Suites: $(. /etc/os-release && echo "${UBUNTU_CODENAME:-$VERSION_CODENAME}")
Components: stable
Architectures: $(dpkg --print-architecture)
Signed-By: /etc/apt/keyrings/docker.asc
EOF

sudo apt update
sudo apt install -y \
    docker-ce \
    docker-ce-cli \
    containerd.io \
    docker-buildx-plugin \
    docker-compose-plugin

# run docker without sudo
sudo usermod -aG docker dad

# for the changes to take effect:
newgrp docker

# configuring Docker to Start automatically
sudo systemctl enable docker
sudo systemctl enable containerd
```

Log out and log in again so the `docker` group takes effect:

```bash
exit
ssh dad@100.91.57.78

docker version
docker compose version
docker run --rm hello-world
```

Limit the size of container logs, otherwise they will fill the whole disk over time:

```bash
sudo tee /etc/docker/daemon.json > /dev/null <<'EOF'
{
  "log-driver": "json-file",
  "log-opts": { "max-size": "20m", "max-file": "5" }
}
EOF
sudo systemctl restart docker
```

## Step 9. Directory structure

```bash
mkdir -p /home/dad/workspace/backups/dump
mkdir -p /home/dad/workspace/infrastructure/letsencrypt
mkdir -p /home/dad/workspace/www

touch /home/dad/workspace/infrastructure/letsencrypt/dad.json
chmod 600 /home/dad/workspace/infrastructure/letsencrypt/dad.json
```

> Permissions `600` on `dad.json` are required. Otherwise Traefik refuses to work with the certificates.

## Step 10. Shared network and Traefik

The network is created once. Traefik sees all projects through it:

```bash
docker network create reverse-proxy
```

Create `docker-compose.yaml` for Traefik. Its content is in the project: [`docs/infrastructure/docker-compose.yaml`](infrastructure/docker-compose.yaml). There are two ways to copy it:
- after step 12 (cloning the project): `cp /home/dad/workspace/www/vacancy-grabber/docs/infrastructure/docker-compose.yaml /home/dad/workspace/infrastructure/`;
- or right away, with `nano /home/dad/workspace/infrastructure/docker-compose.yaml` and pasting the content.

Email for Let's Encrypt:

```bash
cd /home/dad/workspace/infrastructure
echo 'ACME_EMAIL=you@example.com' > .env
```

Start it:

```bash
cd /home/dad/workspace/infrastructure
docker compose up -d
docker compose ps
docker compose logs -f traefik           # Ctrl+C to exit
```

Check: `curl -I http://100.91.57.78` should return `404 page not found`. This is expected: Traefik works, there are just no routes yet.

The Traefik dashboard is not open to the outside. You can view it through an SSH tunnel:

```bash
# on your local computer
ssh -L 8080:127.0.0.1:8080 dad@100.91.57.78
# then open http://localhost:8080 in the browser
```

## Step 11. Server access to the git repository

Create a separate key on the server and add it to the repository as a **Deploy key** (read-only):

```bash
ssh-keygen -t ed25519 -C "dad@vps-main" -f ~/.ssh/id_ed25519 -N ""
cat ~/.ssh/id_ed25519.pub
```

Where to add the key:
- **GitHub:** repository → *Settings → Deploy keys → Add deploy key* (do not tick write access);
- **Bitbucket:** repository → *Repository settings → Access keys → Add key*.

Check:

```bash
ssh -T git@github.com            # or: ssh -T git@bitbucket.org
```

## Step 12. Clone the project

```bash
cd /home/dad/workspace/www
git clone git@github.com:vtsot/vacancy-grabber.git vacancy-grabber
# or: git clone git@bitbucket.org:vitalii-sotnykov/vacancy-grabber.git vacancy-grabber
cd /home/dad/workspace/www/vacancy-grabber
```

> The folder name `vacancy-grabber` becomes the compose project name and is used in container and volume names. Do not rename the folder after the first start, otherwise Docker creates new empty volumes for the database.

## Step 13. Project secrets

```bash
cd /home/dad/workspace/www/vacancy-grabber
cp .env.prod.dist .env.prod.local
chmod 600 .env.prod.local

# generate values
openssl rand -hex 32        # → APP_SECRET
openssl rand -base64 24     # → MYSQL_PASSWORD
openssl rand -base64 24     # → MYSQL_ROOT_PASSWORD
openssl rand -base64 24     # → JWT_PASSPHRASE

nano .env.prod.local
```

What to fill in:

| Variable | Value |
|---|---|
| `APP_DOMAIN` | server IP, e.g. `100.91.57.78` |
| `APP_SECRET` | output of `openssl rand -hex 32` |
| `DEFAULT_URI` | `http://100.91.57.78/` |
| `CORS_ALLOW_ORIGIN` | `'^https?://100\.91\.57\.78$'` |
| `MYSQL_DATABASE` / `MYSQL_USER` | e.g. `vacancy_grabber` / `vacancy_grabber` |
| `MYSQL_PASSWORD` / `MYSQL_ROOT_PASSWORD` | generated passwords |
| `DATABASE_URL` | `mysql://vacancy_grabber:<MYSQL_PASSWORD>@mysql:3306/vacancy_grabber?serverVersion=8.0.32&charset=utf8mb4` |
| `JWT_PASSPHRASE` | generated string |

> If a password contains `@ : / ? # %`, those characters must be URL-encoded in `DATABASE_URL`. It is simpler to generate passwords without them: `openssl rand -hex 24`.

## Step 14. First project start

```bash
cd /home/dad/workspace/www/vacancy-grabber

make prod-up         # build images and start (first build takes 5–15 minutes)
make prod-ps         # all containers Up, mysql healthy
make prod-jwt-keys   # once: JWT keys in the "jwt" docker volume
make prod-migrate    # DB migrations + messenger queue tables
```

If `make` is not available, the same commands without it:

```bash
docker compose -f docker-compose.prod.yaml --env-file .env.prod.local up -d --build
docker compose -f docker-compose.prod.yaml --env-file .env.prod.local exec php-fpm php bin/console doctrine:migrations:migrate --no-interaction
```

**Administrator.** The project has no console command for creating a user. Options:
- move the database from your local machine (see step 17, "Restore");
- add the user directly to the users table through the MySQL console:
  ```bash
  docker compose -f docker-compose.prod.yaml --env-file .env.prod.local exec mysql \
    mysql -u root -p vacancy_grabber
  ```
  You can get a password hash with: `make prod-console` → `php bin/console security:hash-password`.

## Step 14a. Frontend build (yarn install && yarn build)

You do **not** need Node.js or yarn on the server. The frontend is built inside Docker, in the `assets` stage of the root `Dockerfile`:

1. `yarn install --frozen-lockfile` installs the packages from `yarn.lock`;
2. `yarn build` (`encore production`) compiles the assets into `public/assets/`;
3. the result is copied into both images: `app` (needs `entrypoints.json` / `manifest.json`) and `nginx` (serves the files).

This happens automatically on every `make prod-up` / `make prod-build`. Docker reuses the cached result when `package.json`, `yarn.lock`, `assets/` and `webpack.config.js` have not changed. The yarn download cache is also kept between builds, so after a dependency change packages are not downloaded again (this helps on a slow network; most of the install time is unpacking and linking).

Run **only** the frontend build, for example to check that it passes before restarting the stack:

```bash
cd /home/dad/workspace/www/vacancy-grabber
make prod-assets
```

The log must show `yarn install` and `encore production`, and end without `ERROR`.

Check that the compiled assets are in the running image:

```bash
docker compose -f docker-compose.prod.yaml --env-file .env.prod.local exec php-fpm ls public/assets | head
docker compose -f docker-compose.prod.yaml --env-file .env.prod.local exec php-fpm cat public/assets/entrypoints.json | head -20
```

**Small VPS (1–2 GB RAM).** Webpack can run out of memory. Make sure swap is enabled (step 6) and lower the Node heap limit (default 2048 MB):

```bash
docker compose -f docker-compose.prod.yaml --env-file .env.prod.local build --build-arg NODE_MAX_OLD_SPACE=1024
make prod-up
```

**Build from scratch** (if you suspect a broken cache):

```bash
docker builder prune -f
make prod-up
```

## Step 15. Check

```bash
curl -I http://100.91.57.78/           # 200 or 302 (redirect to login)
make prod-logs                          # logs of all containers
```

In the browser: `http://100.91.57.78/admin/source/list`. The page should open with styles.
Click "Grab" on a source. In `make prod-logs`, the `worker` container should show the message being processed.

## Step 16. Updating the project

```bash
cd /home/dad/workspace/www/vacancy-grabber
git pull
make prod-up
make prod-migrate
docker image prune -f                  # remove old images
```

`make prod-up` also rebuilds the frontend (`yarn install && yarn build`) when frontend files changed. No separate step is needed (see step 14a).

## Step 17. Database backups

Script `/home/dad/workspace/backups/backup-vacancy-grabber.sh`:

```bash
touch /home/dad/workspace/backups/backup-vacancy-grabber.sh
cat > /home/dad/workspace/backups/backup-vacancy-grabber.sh <<'EOF'
#!/usr/bin/env bash
set -euo pipefail
cd /home/dad/workspace/www/vacancy-grabber
FILE=/home/dad/workspace/backups/dump/vacancy-grabber-$(date +%F_%H%M).sql.gz
docker compose -f docker-compose.prod.yaml --env-file .env.prod.local exec -T mysql \
  sh -c 'exec mysqldump --single-transaction --routines -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' \
  | gzip > "$FILE"
find /home/dad/workspace/backups/dump -name 'vacancy-grabber-*.sql.gz' -mtime +7 -delete
EOF
chmod 700 /home/dad/workspace/backups/backup-vacancy-grabber.sh
/home/dad/workspace/backups/backup-vacancy-grabber.sh && ls -lh /home/dad/workspace/backups/dump
```

Cron (every day at 03:00):

```bash
( crontab -l 2>/dev/null; echo '0 3 * * * /home/dad/workspace/backups/backup-vacancy-grabber.sh >> /home/dad/workspace/backups/backup.log 2>&1' ) | crontab -
crontab -l
```

**Restore / move the local database to the server:**

```bash
# locally: make a dump and copy it to the server
scp dump.sql.gz dad@100.91.57.78:/home/dad/workspace/backups/

# on the server
cd /home/dad/workspace/www/vacancy-grabber
cat .env.prod.local
docker compose -f docker-compose.prod.yaml --env-file .env.prod.local exec -it mysql mysql -uroot -p
use vacancy_grabber;
SOURCE /var/www/html/file.sql;

# or on the server
cd /home/dad/workspace/www/vacancy-grabber
gunzip -c /home/dad/workspace/backups/dump.sql.gz | docker compose -f docker-compose.prod.yaml --env-file .env.prod.local \
  exec -T mysql sh -c 'exec mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"'
make prod-migrate
```

> It is better to also copy backups off the server (rsync / rclone to cloud storage).

## Step 18. Switching from IP to a domain (HTTPS)

1. At your domain registrar, create a DNS **A** record: `vacancy-grabber.example.com → 100.91.57.78`. Check: `dig +short vacancy-grabber.example.com`.
2. In `.env.prod.local`:
   ```
   APP_DOMAIN=vacancy-grabber.example.com
   DEFAULT_URI=https://vacancy-grabber.example.com/
   CORS_ALLOW_ORIGIN='^https://vacancy-grabber\.example\.com$'
   ```
3. In `docker-compose.prod.yaml`, uncomment the "Stage 2" labels block of the `nginx` service.
4. Apply the changes:
   ```bash
   make prod-up
   docker logs -f traefik            # certificate issuance
   ```
5. Open `https://vacancy-grabber.example.com`. HTTP redirects to HTTPS. The certificate is saved in `/home/dad/workspace/infrastructure/letsencrypt/dad.json` and renewed automatically.

> After switching to the domain, the project is no longer available by IP. This is intended: the IP becomes free.

## Step 19. Adding the next project

Example: `site1.com` in the folder `/home/dad/workspace/www/site1.com`.

```bash
cd /home/dad/workspace/www
git clone <repo> site1.com
```

In its `docker-compose`, on the web service (nginx / app):

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

For the subdomain `sub-1.site1.com` (folder `/home/dad/workspace/www/sub-1.site1.com`) everything is the same, with the rule ``Host(`sub-1.site1.com`)`` and its own router names.

Rules for every new project:
- **Unique names** for routers, services and middlewares in labels (`site1`, `sub1-site1`, …). The same names in two projects conflict.
- **No `ports:`.** Only `expose:` and the `reverse-proxy` network.
- **Each project's database stays in its internal network.** Only the web container joins `reverse-proxy`.
- **Folder name = compose project name.** Folders must differ, which is already the case because each domain has its own folder.
- **Only one project can answer on the IP.** The ``Host(`<IP>`)`` rule matches one router, so all other projects need domains (or subdomains).
- Before starting, check that the domain's DNS already points to the server. Otherwise Let's Encrypt will not issue a certificate, and after several failed attempts it temporarily blocks requests.

## Step 20. Cheat sheet and troubleshooting

```bash
# Traefik
cd /home/dad/workspace/infrastructure && docker compose logs -f traefik
docker compose restart traefik

# project
cd /home/dad/workspace/www/vacancy-grabber
make prod-ps
make prod-logs
make prod-console
make prod-down

# resources
docker stats
df -h && docker system df
```

| Symptom | Cause / fix |
|---|---|
| `404 page not found` from Traefik | The request `Host` does not match `APP_DOMAIN`, or the nginx container is not in the `reverse-proxy` network. Check: `docker inspect <container>` → Labels / Networks; Traefik dashboard. |
| `502 Bad Gateway` | nginx cannot reach php-fpm: `make prod-ps`, `make prod-logs`. |
| `Gateway Timeout` | Traefik and nginx are in different networks. Check the `traefik.docker.network=reverse-proxy` label. |
| Certificate is not issued | DNS does not point to the VPS yet, or port 80 is closed. `dad.json` must have permissions `600`. See `docker logs traefik`. |
| Build fails: `Killed` / exit 137 | Not enough memory, add swap (step 6). |
| Site without styles | The nginx image was built from old code: `make prod-up` (with `--build`). |
| `JavaScript heap out of memory` during the build | Webpack needs more memory: enable swap (step 6), lower `NODE_MAX_OLD_SPACE` (step 14a). |
| `yarn install` fails with a network/timeout error | Temporary registry problem: run `make prod-up` again (the timeout is already raised to 10 min). |
| `Your lockfile needs to be updated` | `package.json` was changed without updating `yarn.lock`: run `yarn install` locally, commit `yarn.lock`, then `git pull` on the server. |
| `client version ... is too old` in Traefik logs | An old Traefik does not work with a new Docker. Update the image: `docker compose pull && docker compose up -d`. |
