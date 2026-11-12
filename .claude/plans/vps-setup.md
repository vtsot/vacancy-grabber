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

### Project facts
- project: `vacancy-grabber`; server folder: `/home/dad/workspace/www/vacancy-grabber`
- domain: `vacancy-grabber.lucked.me` (zone `lucked.me` is on Cloudflare); the site must be public and **HTTPS only** (`http://` redirects to `https://`)
- `100.91.57.78` is the **Tailscale** IP of the VPS: reachable only inside the tailnet (SSH, admin), **not** from the internet
- the VPS has **no public IP**, so an A record + Let's Encrypt cannot work. The domain is published with a **Cloudflare Tunnel** (`cloudflared` container): an outbound connection from the VPS to Cloudflare. Cloudflare terminates HTTPS and does the HTTP→HTTPS redirect.
- the project already runs by the Tailscale IP from that same folder, so step 18 only switches it to the domain (no folder move, the database and volumes are untouched)

```
Browser ──https──► Cloudflare edge (certificate, "Always Use HTTPS")
                        │  tunnel (outbound from the VPS)
                        ▼
                 cloudflared ──http──► traefik:80 ──► nginx (Host: vacancy-grabber.lucked.me) ─► php-fpm / worker ─► mysql
```

Consequences:
- Traefik's Let's Encrypt resolver and the "Stage 2" HTTPS labels are **not used** for this domain (they stay for a future public IP, step 18, variant B).
- A Traefik `redirectscheme` middleware must **not** be enabled with the tunnel: every tunnel request reaches Traefik over plain `web:80`, so it would redirect forever. The redirect is done by Cloudflare.
- The tunnel creates the DNS record itself (proxied CNAME, orange cloud is mandatory for tunnels).

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
- `--entrypoints.web.forwardedheaders.trustedips=172.16.0.0/12`: trust `X-Forwarded-*` from `cloudflared` (docker network). Without it Traefik overwrites `X-Forwarded-Proto: https` with `http`, so the app builds `http://` links and the real client IP is lost (step 18).
- A `cloudflared` service (pinned `cloudflare/cloudflared` image, `command: tunnel --no-autoupdate run`, `TUNNEL_TOKEN` from `.env`, network `reverse-proxy`, `depends_on: traefik`) in the same compose file (step 18).
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
With the Cloudflare Tunnel (step 18) the "Stage 2" labels stay commented out: TLS is terminated by Cloudflare and Traefik only serves plain HTTP on `web`. They are needed only if the VPS gets a public IP (step 18, variant B).

`APP_DOMAIN=<IP>` works because when you open the site by IP, the `Host` header equals the IP. `framework.yaml` already trusts `X-Forwarded-*` from private networks (`trusted_proxies`), and `cookie_secure: auto` works over HTTP.

## 1. Contents of `docs/vps-setup.md` (step by step, with commands)
1. **First login as root.** `apt update && apt upgrade -y`, timezone, `hostnamectl`.
2. **User `dad`.** `adduser dad`, `usermod -aG sudo dad`, copy `authorized_keys`, check login as `dad`.
3. **SSH hardening.** `/etc/ssh/sshd_config.d/99-hardening.conf`: `PermitRootLogin no`, `PasswordAuthentication no`. Before `systemctl restart ssh`, check login in a second window.
4. **Firewall.** `ufw allow OpenSSH`, `80/tcp`, `443/tcp`, `ufw enable`. Warning: Docker bypasses ufw for published ports, so only Traefik may publish ports (`ports:`). The VPS has no public IP and the tunnel (step 18) is outbound only, so 80/443 are not reachable from the internet and are not needed by the tunnel; they stay open as planned (tailnet / future public IP).
5. **fail2ban + unattended-upgrades.**
6. **Swap 2–4 GB.** The webpack build inside Docker uses a lot of RAM; on a small VPS without swap it will hit OOM.
7. **Utilities.** `apt install -y git make curl ca-certificates`.
8. **Docker Engine + compose plugin** from Docker's official apt repository (commands for noble). `usermod -aG docker dad`, log in again, `docker run hello-world`.
9. **Directory structure.** `mkdir -p /home/dad/workspace/{infrastructure/letsencrypt,www,backups/dump}`, `touch letsencrypt/dad.json && chmod 600`.
10. **Network and Traefik.** `docker network create reverse-proxy`. Put `docker-compose.yaml` and `.env` (ACME_EMAIL) into `infrastructure/`, `docker compose up -d`, `docker compose logs`. In step 18 the same compose file gets the `cloudflared` service, the `forwardedheaders.trustedips` flag and `TUNNEL_TOKEN` in `.env`.
11. **Repository access.** `ssh-keygen -t ed25519` as `dad`, add the public key as a Deploy key (GitHub `vtsot/vacancy-grabber` / Bitbucket), `ssh -T git@github.com`.
12. **Cloning.** `cd /home/dad/workspace/www && git clone <repo> vacancy-grabber`.
13. **Secrets.** `cp .env.prod.dist .env.prod.local`, `chmod 600`. Fill in: `APP_DOMAIN=<IP>`, `APP_SECRET` (`openssl rand -hex 32`), MySQL passwords, `DATABASE_URL`, `JWT_PASSPHRASE`, `DEFAULT_URI=http://<IP>/` (IP = the Tailscale IP `100.91.57.78` until step 18). After step 18: `APP_DOMAIN=vacancy-grabber.lucked.me`, `DEFAULT_URI=https://vacancy-grabber.lucked.me/`, `CORS_ALLOW_ORIGIN='^https://vacancy-grabber\.lucked\.me$'`.
14. **First start.** `make prod-up`, `make prod-jwt-keys`, `make prod-migrate`, `make prod-ps`. Creating an admin: the project has no command for it, so describe an option via `prod-console`.
15. **Check.** `curl -I http://<IP>/`, browser `http://<IP>/admin/...`, `make prod-logs` (the worker logs show messages being processed).
16. **Updating the project.** `git pull && make prod-up && make prod-migrate`, `docker image prune -f`.
17. **DB backups.** Script `/home/dad/workspace/backups/backup-vacancy-grabber.sh`: `mysqldump` via `docker compose exec` → `/home/dad/workspace/backups/dump/vacancy-grabber-<date>.sql.gz`, 7-day rotation in `dump/`. Cron daily at 03:00, log `/home/dad/workspace/backups/backup.log`. Restore from `dump/`.
18. **Switching from the Tailscale IP to the public domain `vacancy-grabber.lucked.me` (Cloudflare Tunnel, HTTPS only).**
    Goal: `https://vacancy-grabber.lucked.me` is public; `http://…` redirects to `https://…`; the stack in `/home/dad/workspace/www/vacancy-grabber` keeps running as is.

    **18.1 Cloudflare Zero Trust and the tunnel** (browser)
    1. https://one.dash.cloudflare.com → pick the account (first time: create a Zero Trust team name, plan Free).
    2. Networks → Tunnels → Create a tunnel → type **Cloudflared** → name `vps` → Save.
    3. Copy the **token** from the "Docker" install command (the long string after `--token`). Do not run the command it shows; the container is created in 18.3.
    4. Next → **Published application routes** (older UI: **Public hostname**), add a route: **Subdomain** `vacancy-grabber` (only the label, no dots, no `.lucked.me`), **Domain** `lucked.me` (drop-down of the account's zones), **Path** empty, **Type** `HTTP`, **URL** `traefik:80` (no `http://`). Save. The subdomain needs nothing in DNS beforehand: Cloudflare creates the proxied CNAME `vacancy-grabber` → `<tunnel-id>.cfargotunnel.com` itself; delete any existing record of that name first; change or remove it via the tunnel route. Another subdomain later = another route on the same tunnel; the domain root = empty Subdomain. It must match `APP_DOMAIN` exactly. (Synced to `docs/vps-setup.md` step 18.1.)
       Cloudflare creates the DNS record `vacancy-grabber` → `<tunnel-id>.cfargotunnel.com` (proxied) automatically. Do not create an A record by hand; if an old record with that name exists (DNS → Records), delete it first.
       `traefik:80` is the Traefik container name on the `reverse-proxy` network. The original `Host` header (`vacancy-grabber.lucked.me`) is preserved, and that is what the Traefik router matches.

    **18.2 Cloudflare HTTPS-only settings** (dashboard → `lucked.me` → SSL/TLS)
    - Overview: mode **Full** (not Flexible).
    - Edge Certificates: **Always Use HTTPS = On** (this is the `http → https` 301 redirect, done at the Cloudflare edge), **Minimum TLS Version = 1.2**, **Automatic HTTPS Rewrites = On**.
    - HSTS: leave off for now; enable only after https is verified (it is hard to undo).

    **18.3 Run cloudflared next to Traefik** (server, SSH over Tailscale)
    ```bash
    cd /home/dad/workspace/infrastructure
    nano .env                                  # add: TUNNEL_TOKEN=<token from 18.1>
    chmod 600 .env
    nano docker-compose.yaml                   # add the cloudflared service and the Traefik flag (below)
    docker compose up -d
    docker compose ps                          # traefik and cloudflared Up
    docker compose logs cloudflared | tail     # "Registered tunnel connection" (x4)
    ```
    Additions to `docker-compose.yaml`:
    ```yaml
        traefik:
            command:
                # ... existing flags ...
                - --entrypoints.web.forwardedheaders.trustedips=172.16.0.0/12
        cloudflared:
            image: cloudflare/cloudflared:<pinned version>
            container_name: cloudflared
            restart: unless-stopped
            command: tunnel --no-autoupdate run
            environment:
                - TUNNEL_TOKEN=${TUNNEL_TOKEN:?set TUNNEL_TOKEN in .env}
            networks:
                - reverse-proxy
            depends_on:
                - traefik
    ```
    In the dashboard (Networks → Tunnels) the tunnel status must be **Healthy**. Without `trustedips`, Traefik replaces `X-Forwarded-Proto: https` with `http`: the app generates `http://` links and non-Secure cookies, and the client IP is lost.

    **18.4 Back up before switching** (server)
    ```bash
    /home/dad/workspace/backups/backup-vacancy-grabber.sh && ls -lh /home/dad/workspace/backups/dump | tail -3
    cp /home/dad/workspace/www/vacancy-grabber/.env.prod.local /home/dad/workspace/backups/env.prod.local.ip-stage
    chmod 600 /home/dad/workspace/backups/env.prod.local.ip-stage
    ```

    **18.5 Switch the project to the domain** (server)
    ```bash
    cd /home/dad/workspace/www/vacancy-grabber
    nano .env.prod.local
    ```
    ```
    APP_DOMAIN=vacancy-grabber.lucked.me
    DEFAULT_URI=https://vacancy-grabber.lucked.me/
    CORS_ALLOW_ORIGIN='^https://vacancy-grabber\.lucked\.me$'
    ```
    ```bash
    make prod-up      # recreates containers with the new env and the new Host() rule; the build is cached
    make prod-ps
    ```
    `APP_DOMAIN` feeds the Traefik rule ``Host(`vacancy-grabber.lucked.me`)`` on the plain `web` entrypoint. The "Stage 2" HTTPS labels stay commented and no redirect middleware is added (it would loop). No migrations and no new JWT keys: same folder = same compose project = same volumes.
    After this, `http://100.91.57.78/` (Tailscale) stops answering, because the router now matches only the domain. To keep a private way in, the rule can be ``Host(`${APP_DOMAIN}`) || Host(`100.91.57.78`)`` (optional).

    **18.6 Verify** (from a machine that is not on the tailnet)
    ```bash
    curl -sI http://vacancy-grabber.lucked.me/ | head -5     # 301, Location: https://vacancy-grabber.lucked.me/ (server: cloudflare)
    curl -sI https://vacancy-grabber.lucked.me/ | head -5    # 200 or 302 to /auth/login, valid certificate (no -k)
    ```
    Browser (a phone on mobile data proves it is public): `https://vacancy-grabber.lucked.me/admin/` logs in, cookies are Secure; "Grab" a source and check `make prod-logs` for the worker.
    Client IP check (the API rate limiter and login throttling key on it): temporarily add `--accesslog.fields.headers.names.X-Forwarded-For=keep` to Traefik, open the site from the phone, and check that the logged `X-Forwarded-For` starts with the phone's public IP, not a Cloudflare or `172.x` address. Remove the flag afterwards.

    **18.7 Security notes**
    - Nothing is exposed on the VPS: the tunnel is outbound only; the Traefik dashboard stays on `127.0.0.1:8080`.
    - `TUNNEL_TOKEN` only in `infrastructure/.env` (chmod 600), never in git. If it leaks: Tunnels → `vps` → refresh token, update `.env`, `docker compose up -d`.
    - Later, optionally: Cloudflare WAF rate limiting rules and Bot Fight Mode for `/api/auth/login` and `/admin/login`.

    **18.8 Rollback**
    ```bash
    cd /home/dad/workspace/www/vacancy-grabber
    cp /home/dad/workspace/backups/env.prod.local.ip-stage .env.prod.local
    make prod-up                                                              # back to http://100.91.57.78 over Tailscale
    cd /home/dad/workspace/infrastructure && docker compose stop cloudflared  # the domain goes offline (Cloudflare Error 1033)
    ```

    **18.9 Variant B: the VPS gets a public IP later.** No tunnel is needed then: DNS-only A record → the public IP (check `dig +short vacancy-grabber.lucked.me` first), Traefik issues the certificate: uncomment the "Stage 2" labels (including the `redirectscheme` middleware) in `docker-compose.prod.yaml`, `make prod-up`. Remove the tunnel public hostname and the `cloudflared` service so there are not two paths to the same site.
19. **Adding the next project** (`/home/dad/workspace/www/site1.com`, `sub-1.site1.com`):
    - unique router/service names in labels;
    - connected to the `reverse-proxy` network;
    - no `ports:`;
    - a unique compose project name (folder name or `name:` in compose).
    Why two projects cannot both live "on the IP": only one project answers on the IP via the Host rule; the others need domains. vacancy-grabber leaves the IP after step 18. With the tunnel, each new domain is one more **public hostname** on the same tunnel (Service `HTTP`, URL `traefik:80`) plus the usual Traefik router labels (no certificate labels, no redirect middleware).
20. **Cheat sheet / troubleshooting.** 404 from Traefik (wrong Host / container not in the network), 502 (nginx cannot reach php-fpm), Let's Encrypt rate limits (only for variant B), Cloudflare Tunnel problems (see the rows to add to the table: tunnel DOWN / Error 1033, 502 from Cloudflare, redirect loop, wrong client IP, `http://` not redirecting).
    New table rows:
    - Error 1033 or tunnel `DOWN`: `cloudflared` is not running or the token is wrong: `docker compose logs cloudflared`.
    - 502 from Cloudflare: the public hostname Service URL is not `http://traefik:80`, or `cloudflared` is not in the `reverse-proxy` network.
    - Endless redirect to https: a Traefik `redirectscheme` middleware is enabled; remove it (Cloudflare redirects).
    - Everybody throttled as one IP / `http://` links in pages: `forwardedheaders.trustedips` is missing in Traefik (18.3).
    - `http://` does not redirect: "Always Use HTTPS" is off (18.2).

## Files touched
- New: `docs/vps-setup.md`, `docs/infrastructure/docker-compose.yaml`, `docs/infrastructure/.env.dist`, `.claude/plans/vps-setup.md`
- Edited: `docker-compose.prod.yaml` (Traefik labels instead of `VIRTUAL_*`), `.env.prod.dist`, `readme.md`
- Follow-ups for the domain/tunnel (step 18): `docs/infrastructure/docker-compose.yaml` (`cloudflared` service + `forwardedheaders.trustedips`), `docs/infrastructure/.env.dist` (`TUNNEL_TOKEN=`), `.env.prod.dist` (example domain `vacancy-grabber.lucked.me`), `docs/vps-setup.md` (same step 18)

## Verification (local)
1. `docker compose -f docker-compose.prod.yaml --env-file <tmp env> config` passes without errors, and the labels contain the IP.
2. Start Traefik from `docs/infrastructure` locally on free ports (for example `8080:80`), plus the prod stack with `APP_DOMAIN=localhost`. Check that `curl -H 'Host: localhost' http://127.0.0.1:8080/` returns the app page. If ports 80/443 are taken by the local nginx-proxy, limit the check to `config`.
3. After the check, remove the temporary containers, volumes and env.
4. On the server after step 18: `curl -sI http://vacancy-grabber.lucked.me/` gives 301 to https, `curl -sI https://vacancy-grabber.lucked.me/` gives 200/302 with a valid certificate, the tunnel is Healthy, and the logged client IP is the real one (18.6).
