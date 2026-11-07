# Frontend build (yarn install && yarn build) on the prod VPS

## Context
The user wants `yarn install && yarn build` to run on the prod VPS. The root `Dockerfile` **already does this**. The `assets` stage (`Dockerfile:44-59`) runs `yarn install --frozen-lockfile` and `yarn build` inside `node:24-alpine` whenever `make prod-up` / `docker compose ... up --build` runs on the server. The VPS needs no Node/yarn on the host.

What is missing:
- `docs/vps-setup.md` never says this, so it looks like a manual step was forgotten.
- There is a manual way to check the build without starting the stack.
- Rebuilds on a small VPS are slow (yarn downloads everything every time `package.json`/`yarn.lock` change) and can run out of memory.

Approach: keep the build inside Docker (no Node on the host, same result everywhere). Make it faster and more robust, and document it.

## Changes

### 1. `Dockerfile`, `assets` stage
- Yarn cache as a BuildKit cache mount so repeated builds on the VPS reuse downloaded packages:
  ```dockerfile
  RUN --mount=type=cache,target=/usr/local/share/.cache/yarn \
      yarn install --frozen-lockfile --non-interactive --network-timeout 600000
  ```
- Node memory limit, configurable for small VPSes:
  ```dockerfile
  ARG NODE_MAX_OLD_SPACE=2048
  ENV NODE_OPTIONS=--max-old-space-size=${NODE_MAX_OLD_SPACE}
  RUN NODE_ENV=production TS_TRANSPILE_ONLY=1 yarn build
  ```
- A short comment at the top of the stage: "this is where yarn install && yarn build run; triggered by make prod-up / prod-build".

### 2. `makefile`
New target:
```make
prod-assets: ## [prod] Run yarn install && yarn build only (assets stage), to check the frontend build
	DOCKER_BUILDKIT=1 docker build --target assets --progress=plain -t vacancy-grabber-prod-assets .
```
Add it to `.PHONY`. `prod-up` stays the normal way: it rebuilds the assets and bakes them into both images. The app image needs `public/assets/entrypoints.json` + `manifest.json` (`config/packages/webpack_encore.yaml:35`), and nginx serves the files.

### 3. `docs/vps-setup.md`
- New **Step 14a "Frontend build (yarn)"** after the first start:
  - `make prod-up` runs `yarn install && yarn build` inside Docker; no Node on the host.
  - Run only the frontend build: `make prod-assets`.
  - Low-memory VPS: `docker compose -f docker-compose.prod.yaml --env-file .env.prod.local build --build-arg NODE_MAX_OLD_SPACE=1024`, plus swap (step 6).
  - Build from scratch (cache problems): `docker builder prune -f`, then `make prod-up`.
  - Check that the assets are in the image: `docker compose ... exec php-fpm ls public/assets | head` / `cat public/assets/entrypoints.json`.
- Step 16 "Updating": add a note that frontend changes are rebuilt automatically by `make prod-up`.
- Troubleshooting table: "yarn install timeout / network" → retry, `--network-timeout` already set; "`JavaScript heap out of memory`" → `NODE_MAX_OLD_SPACE` + swap.

## Files
- `Dockerfile` (assets stage only)
- `makefile` (`prod-assets` target)
- `docs/vps-setup.md` (step 14a, step 16 note, troubleshooting rows)

## Verification
1. `make prod-assets` locally: build succeeds and the log shows `yarn install` + `encore production`.
2. Run it a second time after touching `package.json`: `yarn install` is noticeably faster (cache mount).
3. `docker build --target app .` succeeds; `docker run --rm <img> ls public/assets/entrypoints.json` exists.
4. Remove the test images afterwards.
