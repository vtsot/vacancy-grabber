# VPS setup: Traefik + multiple projects

## Context
There is a VPS (Ubuntu 24.04, SSH access). It needs to host several projects in Docker behind one reverse proxy. The first project is vacancy-grabber. Until there is a domain, it must be available at `http://<VPS IP>`. Later the domains `site1.com`, `site2.com`, `sub-1.site1.com`, etc. will be added.

Right now `docker-compose.prod.yaml` is built for **nginx-proxy** (`VIRTUAL_HOST`). The user wants **Traefik**, with certificates in `letsencrypt/dad.json` instead of `acme.json`. So two things are needed:
1. A detailed step-by-step VPS setup guide (written in Russian, later translated to English).
2. Switching the project's prod stack to Traefik labels.

Structure on the server (user `dad`):
```
/home/dad/workspace/
├── backups/                   # backup scripts (*.sh) + backup.log
│   └── dump/                  # archived DB dumps (*.sql.gz), kept 7 days
├── infrastructure/            # Traefik (shared reverse proxy)
│   ├── docker-compose.yaml
│   ├── .env                   # ACME_EMAIL
│   └── letsencrypt/dad.json   # certificates, chmod 600
└── www/
    └── vacancy-grabber/       # git clone of the project (future: site1.com/, sub-1.site1.com/ ...)
```

## Deliverables
1. **`docs/vps-setup.md`**: new file, a detailed guide with all commands.
2. **`docs/infrastructure/docker-compose.yaml`**: Traefik template, copied to `/home/dad/workspace/infrastructure/` on the server.
3. **`docker-compose.prod.yaml`**: remove `VIRTUAL_*`/`LETSENCRYPT_HOST`, add Traefik labels to `nginx`.
4. **`.env.prod.dist`**: `APP_DOMAIN` = IP by default, `DEFAULT_URI=http://<IP>/`, comments about switching to a domain.
5. **`readme.md`**: replace the nginx-proxy block with a short section linking to `docs/vps-setup.md`.
6. After leaving plan mode, copy this plan to `.claude/plans/vps-setup.md` (project rule).

The internal network between Traefik and the projects is the external docker network `reverse-proxy`. The prod compose already uses that name, so it stays.

## 2. Traefik (`docs/infrastructure/docker-compose.yaml`)
- `traefik:v3.x` (exact version), `restart: unless-stopped`, ports `80:80`, `443:443`.
- Docker provider: `exposedByDefault=false`, `network=reverse-proxy`.
- Entrypoints `web` (:80) and `websecure` (:443). **No global redirect to https**: while access is by IP, plain HTTP is needed. The redirect is enabled on a project's own router once it has a domain.
- `certificatesresolvers.le.acme.storage=/letsencrypt/dad.json`, `httpchallenge.entrypoint=web`, `email=${ACME_EMAIL}`.
- Volumes: `/var/run/docker.sock:ro` and `./letsencrypt:/letsencrypt`.
- The dashboard is not published to the outside; if needed, use an SSH tunnel.

## 3. Project labels (`docker-compose.prod.yaml`, `nginx` service)
The `${APP_DOMAIN}` reference in the environment is removed, and these labels remain:
```yaml
labels:
  - traefik.enable=true
  - traefik.docker.network=reverse-proxy
  - traefik.http.services.vacancy-grabber.loadbalancer.server.port=80
  # Stage 1: access by IP (HTTP)
  - traefik.http.routers.vacancy-grabber.rule=Host(`${APP_DOMAIN}`)
  - traefik.http.routers.vacancy-grabber.entrypoints=web
  # Stage 2: domain + HTTPS, uncomment after DNS is set up
  # - traefik.http.routers.vacancy-grabber-https.rule=Host(`${APP_DOMAIN}`)
  # - traefik.http.routers.vacancy-grabber-https.entrypoints=websecure
  # - traefik.http.routers.vacancy-grabber-https.tls.certresolver=le
  # - traefik.http.routers.vacancy-grabber.middlewares=vacancy-grabber-redirect
  # - traefik.http.middlewares.vacancy-grabber-redirect.redirectscheme.scheme=https
```
`APP_DOMAIN=<IP>` works because when you open the site by IP, the `Host` header equals the IP. `framework.yaml` already trusts `X-Forwarded-*` from private networks (`trusted_proxies`), and `cookie_secure: auto` works over HTTP.

## 1. Contents of `docs/vps-setup.md` (step by step, with commands)
1. **First login as root.** `apt update && apt upgrade -y`, timezone, `hostnamectl`.
2. **User `dad`.** `adduser dad`, `usermod -aG sudo dad`, copy `authorized_keys`, check login as `dad`.
3. **SSH hardening.** `/etc/ssh/sshd_config.d/99-hardening.conf`: `PermitRootLogin no`, `PasswordAuthentication no`. Before `systemctl restart ssh`, check login in a second window.
4. **Firewall.** `ufw allow OpenSSH`, `80/tcp`, `443/tcp`, `ufw enable`. Warning: Docker bypasses ufw for published ports, so only Traefik may publish ports (`ports:`).
5. **fail2ban + unattended-upgrades.**
6. **Swap 2–4 GB.** The webpack build inside Docker uses a lot of RAM; on a small VPS without swap it will hit OOM.
7. **Utilities.** `apt install -y git make curl ca-certificates`.
8. **Docker Engine + compose plugin** from Docker's official apt repository (commands for noble). `usermod -aG docker dad`, log in again, `docker run hello-world`.
9. **Directory structure.** `mkdir -p /home/dad/workspace/{infrastructure/letsencrypt,www,backups/dump}`, `touch letsencrypt/dad.json && chmod 600`.
10. **Network and Traefik.** `docker network create reverse-proxy`. Put `docker-compose.yaml` and `.env` (ACME_EMAIL) into `infrastructure/`, `docker compose up -d`, `docker compose logs`.
11. **Repository access.** `ssh-keygen -t ed25519` as `dad`, add the public key as a Deploy key (GitHub `vtsot/vacancy-grabber` / Bitbucket), `ssh -T git@github.com`.
12. **Cloning.** `cd /home/dad/workspace/www && git clone <repo> vacancy-grabber`.
13. **Secrets.** `cp .env.prod.dist .env.prod.local`, `chmod 600`. Fill in: `APP_DOMAIN=<IP>`, `APP_SECRET` (`openssl rand -hex 32`), MySQL passwords, `DATABASE_URL`, `JWT_PASSPHRASE`, `DEFAULT_URI=http://<IP>/`.
14. **First start.** `make prod-up`, `make prod-jwt-keys`, `make prod-migrate`, `make prod-ps`. Creating an admin: the project has no command for it, so describe an option via `prod-console`.
15. **Check.** `curl -I http://<IP>/`, browser `http://<IP>/admin/...`, `make prod-logs` (the worker logs show messages being processed).
16. **Updating the project.** `git pull && make prod-up && make prod-migrate`, `docker image prune -f`.
17. **DB backups.** Script `/home/dad/workspace/backups/backup-vacancy-grabber.sh`: `mysqldump` via `docker compose exec` → `/home/dad/workspace/backups/dump/vacancy-grabber-<date>.sql.gz`, 7-day rotation in `dump/`. Cron daily at 03:00, log `/home/dad/workspace/backups/backup.log`. Restore from `dump/`.
18. **Switching to a domain.** DNS A record → IP. In `.env.prod.local`: `APP_DOMAIN=site.com`, `DEFAULT_URI=https://...`. Uncomment the HTTPS labels, `make prod-up`. The certificate appears in `dad.json`.
19. **Adding the next project** (`/home/dad/workspace/www/site1.com`, `sub-1.site1.com`):
    - unique router/service names in labels;
    - connected to the `reverse-proxy` network;
    - no `ports:`;
    - a unique compose project name (folder name or `name:` in compose).
    Why two projects cannot both live "on the IP": only one project answers on the IP via the Host rule; the others need domains.
20. **Cheat sheet / troubleshooting.** 404 from Traefik (wrong Host / container not in the network), 502 (nginx cannot reach php-fpm), Let's Encrypt rate limits.

## Files touched
- New: `docs/vps-setup.md`, `docs/infrastructure/docker-compose.yaml`, `docs/infrastructure/.env.dist`, `.claude/plans/vps-setup.md`
- Edited: `docker-compose.prod.yaml` (Traefik labels instead of `VIRTUAL_*`), `.env.prod.dist`, `readme.md`

## Verification (local)
1. `docker compose -f docker-compose.prod.yaml --env-file <tmp env> config` passes without errors, and the labels contain the IP.
2. Start Traefik from `docs/infrastructure` locally on free ports (for example `8080:80`), plus the prod stack with `APP_DOMAIN=localhost`. Check that `curl -H 'Host: localhost' http://127.0.0.1:8080/` returns the app page. If ports 80/443 are taken by the local nginx-proxy, limit the check to `config`.
3. After the check, remove the temporary containers, volumes and env.
