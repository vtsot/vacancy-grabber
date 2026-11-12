# Update the "vps-setup" plan: public domain vacancy-grabber.lucked.me via Cloudflare Tunnel

## Context
`.claude/plans/vps-setup.md` treats the domain as a generic future step (step 18 is 5 lines, "example.com", Let's Encrypt via Traefik). Facts now known:
- project `vacancy-grabber`, server folder `/home/dad/workspace/www/vacancy-grabber`, domain `vacancy-grabber.lucked.me` (zone `lucked.me` is on Cloudflare)
- `100.91.57.78` is the **Tailscale** IP of the VPS: it works only inside the tailnet, not from the internet
- the VPS has **no public IP**, so the domain cannot be pointed at it with an A record and Let's Encrypt cannot validate the domain
- the site must be public (reachable by everyone) and **HTTPS only**: `http://vacancy-grabber.lucked.me` must redirect to `https://…`
- the user can run a tunnel on the VPS

Decision: **Cloudflare Tunnel** (`cloudflared` container). The tunnel is an outbound connection from the VPS to Cloudflare, so no public IP or open inbound port is needed. Cloudflare terminates HTTPS with its own certificate and does the HTTP→HTTPS redirect at its edge. Tailscale stays for SSH/admin.

```
Browser ──https──► Cloudflare edge (cert, "Always Use HTTPS")
                        │  tunnel (outbound from the VPS)
                        ▼
                 cloudflared ──http──► traefik:80 ──► nginx (Host: vacancy-grabber.lucked.me) ─► php-fpm / mysql
```

Why this differs from the current plan:
- Traefik's Let's Encrypt resolver and the "Stage 2" HTTPS labels in `docker-compose.prod.yaml` are **not used** for this domain (TLS is at Cloudflare). They stay commented.
- A Traefik `redirectscheme` middleware must **not** be enabled: every tunnel request arrives at Traefik over plain `web:80`, so the redirect would loop forever. The redirect is done by Cloudflare.
- The DNS record is created by the tunnel as a **proxied CNAME** (orange cloud is required for tunnels), not a DNS-only A record.

## What changes (only `.claude/plans/vps-setup.md` now; repo files are listed as follow-ups)
1. **Context block**: add "Project facts" (name, folder, domain, Tailscale IP, no public IP, Cloudflare Tunnel) and the diagram above to "How it works".
2. **Replace step 18** with the detailed guide below (18.1–18.9). Keep steps 19 and 20.
3. **Step 13** table: add a second column/line "after step 18" with `APP_DOMAIN=vacancy-grabber.lucked.me`, `DEFAULT_URI=https://vacancy-grabber.lucked.me/`, `CORS_ALLOW_ORIGIN='^https://vacancy-grabber\.lucked\.me$'`.
4. **Step 10 (Traefik)**: mention the future `cloudflared` service and the `forwardedheaders.trustedips` flag (needed by step 18).
5. **Step 4 (firewall)**: note that 80/443 stay open as described but are not needed by the tunnel (outbound only) and are not reachable from the internet without a public IP.
6. **Step 20 table**: add rows: tunnel `HEALTHY/DOWN`, `Error 1033`, `502` from Cloudflare, redirect loop (Traefik redirect middleware enabled), wrong client IP / everyone throttled as one IP, `http://` not redirecting (Always Use HTTPS off).
7. **Deliverables / Files touched / Verification** sections: add the follow-up repo changes (below) and the end-to-end check.

## Follow-up repo changes the guide refers to (not done in this task)
- `docs/infrastructure/docker-compose.yaml`: add a pinned `cloudflare/cloudflared` service (`command: tunnel --no-autoupdate run`, `TUNNEL_TOKEN` from `.env`, network `reverse-proxy`, `depends_on: traefik`) and the Traefik flag `--entrypoints.web.forwardedheaders.trustedips=172.16.0.0/12`.
- `docs/infrastructure/.env.dist`: add `TUNNEL_TOKEN=`.
- `.env.prod.dist`: replace the Stage 2 example domain with `vacancy-grabber.lucked.me`.
- `docs/vps-setup.md`: same step 18 (this task edits only the plan).

## Draft of the new step 18 (to be written into the plan)
**Goal:** `https://vacancy-grabber.lucked.me` is public; `http://…` redirects to `https://…`; the site is served by the existing stack in `/home/dad/workspace/www/vacancy-grabber` without moving it or touching its database.

**18.1 Cloudflare Zero Trust and the tunnel** (browser)
1. https://one.dash.cloudflare.com → pick the account (first time: create a Zero Trust team name, plan Free).
2. Networks → Tunnels → Create a tunnel → type **Cloudflared** → name `vps` → Save.
3. Copy the **token** from the "Docker" install command (the long string after `--token`). Do not run the command it shows; the container is created in 18.3.
4. Next → **Public hostname**: subdomain `vacancy-grabber`, domain `lucked.me`, path empty, Service type `HTTP`, URL `traefik:80`. Save.
   Cloudflare creates the DNS record `vacancy-grabber` → `<tunnel-id>.cfargotunnel.com` (proxied) automatically. Do not create an A record by hand; if an old one exists in DNS → Records, delete it first.
   `traefik:80` is the Traefik container name on the `reverse-proxy` network; the original `Host` header (`vacancy-grabber.lucked.me`) is preserved, which is what the Traefik router matches.

**18.2 Cloudflare HTTPS-only settings** (dashboard → `lucked.me` → SSL/TLS)
- Overview: mode **Full** (not Flexible; for tunnels it is not used for the origin hop, but Flexible causes confusing loops if a record is ever added by hand).
- Edge Certificates: **Always Use HTTPS = On** (this is the `http → https` redirect, 301, done at the edge), **Minimum TLS Version = 1.2**, **Automatic HTTPS Rewrites = On**.
- HSTS: leave off for now; turn on only after `https` is verified (it is hard to undo).

**18.3 Run cloudflared next to Traefik** (server, over Tailscale SSH)
```bash
cd /home/dad/workspace/infrastructure
nano .env                                  # add: TUNNEL_TOKEN=<token from 18.1>; chmod 600 .env
nano docker-compose.yaml                   # add the cloudflared service and the Traefik flag (see snippets)
docker compose up -d
docker compose ps                          # traefik and cloudflared Up
docker compose logs cloudflared | tail     # "Registered tunnel connection" ×4
```
Snippets for `docker-compose.yaml`:
```yaml
    traefik:
        command:
            # ... existing flags ...
            # trust X-Forwarded-* from containers on the docker networks, so the real https scheme and client IP from Cloudflare reach the app
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
In the dashboard (Tunnels) the tunnel status must be **Healthy**.
Without `trustedips`, Traefik replaces `X-Forwarded-Proto: https` with `http`: the app would generate `http://` links and non-Secure cookies, and the client IP would be lost.

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
make prod-up      # recreates containers with the new env and the new Host() rule; cached build
make prod-ps
```
`APP_DOMAIN` feeds the Traefik rule ``Host(`vacancy-grabber.lucked.me`)`` on the plain `web` entrypoint. The stage 2 HTTPS labels stay commented, and no redirect middleware is added (see Context). No migrations and no new JWT keys: same folder = same compose project = same volumes.
After this, `http://100.91.57.78/` (Tailscale) stops answering, because the router now matches only the domain. To keep a private way in, the rule can be `Host(`${APP_DOMAIN}`) || Host(`100.91.57.78`)` (optional; this is what the `web` router would serve to tailnet users).

**18.6 Verify** (from any machine, not through Tailscale)
```bash
curl -sI http://vacancy-grabber.lucked.me/ | head -5     # 301, Location: https://vacancy-grabber.lucked.me/ (server: cloudflare)
curl -sI https://vacancy-grabber.lucked.me/ | head -5    # 200 or 302 to /auth/login, valid certificate (no -k)
```
Browser (phone on mobile data = proof that it is public): `https://vacancy-grabber.lucked.me/admin/` logs in; cookies are Secure; "Grab" a source and check `make prod-logs` for the worker.
Client IP check (the API rate limiter and login throttling key on it): temporarily add `--accesslog.fields.headers.names.X-Forwarded-For=keep` to Traefik, open the site from the phone, and make sure the logged `X-Forwarded-For` starts with the phone's public IP, not a Cloudflare or 172.x address. Remove the flag afterwards.

**18.7 Security notes**
- Nothing is exposed on the VPS: the tunnel is outbound only; the Traefik dashboard stays on `127.0.0.1:8080`.
- Keep `TUNNEL_TOKEN` only in `infrastructure/.env` (chmod 600), never in git. If it leaks, rotate it: Tunnels → `vps` → refresh token, update `.env`, `docker compose up -d`.
- Optional hardening later: Cloudflare WAF rate limiting rules and Bot Fight Mode on `/api/auth/login` and `/admin/login`.

**18.8 Rollback**
```bash
cd /home/dad/workspace/www/vacancy-grabber
cp /home/dad/workspace/backups/env.prod.local.ip-stage .env.prod.local
make prod-up                       # back to http://100.91.57.78 over Tailscale
cd /home/dad/workspace/infrastructure && docker compose stop cloudflared   # the domain goes offline (Cloudflare Error 1033)
```

**18.9 Variant B: the VPS gets a public IP later**
Then no tunnel is needed: DNS-only A record → public IP, Traefik issues the certificate (uncomment the "Stage 2" labels in `docker-compose.prod.yaml` and the `redirectscheme` middleware, `make prod-up`), `dig +short vacancy-grabber.lucked.me` must show the IP first. Remove the tunnel public hostname and the `cloudflared` service to avoid two paths to the same site.

## Verification (after the plan is edited)
- Read the edited plan end to end: numbering is consistent (18.1–18.9, 19, 20), no step says "A record → 100.91.57.78", Tailscale is called Tailscale everywhere, and the 100.x IP is never presented as public.
- `grep -n "example.com" .claude/plans/vps-setup.md`: only step 19 (`site1.com`) generic examples remain.
- Names match the real files: `APP_DOMAIN`, `DEFAULT_URI`, `CORS_ALLOW_ORIGIN` (`.env.prod.dist`), router `vacancy-grabber` (`docker-compose.prod.yaml`), container `traefik` and network `reverse-proxy` (`docs/infrastructure/docker-compose.yaml`).
- Final message lists the follow-up repo changes and offers to apply them and to sync step 18 into `docs/vps-setup.md`.
