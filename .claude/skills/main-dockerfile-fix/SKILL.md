---
name: main-dockerfile-fix
description: Check the main (production) Dockerfile in the repo root against the current project and update it - PHP and Node versions, installed PHP extensions, COPY sources, build stages vs docker-compose.prod.yaml, .dockerignore, non-root user and prod-only build steps, frontend build inputs. Use whenever the user asks to check, audit, fix, update or sync the main/root/prod Dockerfile or the production image, after bumping PHP/Node or adding a PHP extension or a dependency, or when a production docker build fails or the image drifts from the dev setup.
---

# Main Dockerfile check and update

"Main Dockerfile" is `./Dockerfile`: the multi-stage production image (`php-base` → `vendor` → `assets` → `app`, plus `nginx`) built by `docker-compose.prod.yaml`. The dev images in `.docker/*/Dockerfile` are not edited here; they are the reference the production image is compared with, because developers add extensions and versions there first.

## Run

```bash
bash .claude/skills/main-dockerfile-fix/scripts/check.sh        # project root = current dir
bash .claude/skills/main-dockerfile-fix/scripts/check.sh path/to/project
```

Each finding is printed as `RULE  location  message`, followed by a per-rule summary. `INFO` lines are things the script cannot decide (for example an extension that exists only in the dev image); they never fail the check. Exit code: `0` clean, `1` findings, `2` bad project root. It runs on the host and needs no container.

## Rules

| ID | Rule | How to fix |
|---|---|---|
| F1 | `ARG PHP_VERSION` satisfies `composer.json` `require.php` and equals the dev default in `docker-compose.yaml` | Change the ARG (and keep the dev compose default in step if the user wants dev and prod aligned). |
| F2 | Every `ext-*` from composer and every extension of the dev image (except tooling such as excimer/xdebug, and amqp/redis/pdo_pgsql unless a DSN uses them) is installed; `pdo_mysql` when the DB is MySQL | Add it to the `install-php-extensions` line in `php-base`. Keep the line in one place so the layer stays cached. |
| F3 | The node image tag equals the one in `.docker/nodejs/Dockerfile` | Change the `FROM node:...` of the `assets` stage. |
| F4 | Every `COPY`/`ADD` source exists | Fix the path, or drop the line if the file was removed. |
| F5 | Every `target:` in the compose files is a stage of the Dockerfile; unused stages are listed as INFO | Rename the stage or the compose target. |
| F6 | `.dockerignore` excludes `.git`, `vendor`, `node_modules`, `var`, `.env.local`, `.env.prod.local`, `config/jwt` | Add the missing lines: they keep secrets and hundreds of MB out of the build context and keep `COPY . .` cache-friendly. |
| F7 | The `app` stage ends as a non-root `USER`, sets `APP_ENV=prod`; `composer install` uses `--no-dev` | Restore the line. Run order matters: the cache warmup runs as `app` so `var/` is owned by it. |
| F8 | The `assets` stage copies `package.json`, `yarn.lock`, `webpack.config.js`, `tsconfig.json`, `assets/`, `.env` (if webpack reads it) and any `vendor/<pkg>` that `assets/` imports | Add a `COPY` (from the `vendor` stage for vendor JS). |

## Procedure

1. Run the script and read the whole output.
2. Triage. A finding that is not a real problem (for example a dev-only extension, or a `COPY` source that a build step creates) is a false positive: list it separately and do not change the Dockerfile for it. Open `Dockerfile` and the related file (`composer.json`, compose file, `.docker/...`) to confirm each real finding before editing.
3. Update `./Dockerfile` (and `.dockerignore` for F6) with the smallest edit per finding. Keep the stage order and the "copy manifests first, then sources" layering: the order is tuned for layer caching on a small VPS, and a reorder makes every build reinstall dependencies.
4. Run the script again until it exits `0` (INFO lines may remain).
5. Verify the build. `docker build --check .` is cheap. A full `docker build --target app -t vacancy-grabber-app .` and `--target nginx` run composer and webpack and take minutes, so ask the user before running them on the dev machine.
6. Report in chat: the findings, what changed in which file, what was skipped as a false positive, and whether the build was run. Do not commit.

The script greps text, so it cannot see whether an extension is really used by the code, or whether a `COPY` of a directory contains everything a build step needs. Say so in the report when it matters.
