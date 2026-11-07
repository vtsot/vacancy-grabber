# Backups location: /home/dad/workspace/backups

## Context
The user moved backups under the workspace:
- `/home/dad/workspace/backups/`: backup `.sh` scripts (and the cron log);
- `/home/dad/workspace/backups/dump/`: archived dumps (`*.sql.gz`).

`docs/vps-setup.md` steps 9 and 17 (backup script, cron) already use the new paths; the user edited them. What is still out of date:
- `.claude/plans/vps-setup.md` still says `/home/dad/backups` and has no `backups/` in the structure tree.
- In `docs/vps-setup.md`:
  - the restore block still uses the old locations (`scp ... :/home/dad/workspace/backups/`, `gunzip -c /home/dad/workspace/backups/dump.sql.gz`);
  - the directory tree at the top has no `backups/`.
- The restore variant the user added (`SOURCE /var/www/html/file.sql;` inside the mysql container) cannot work. The mysql container has no `/var/www/html` and no project or backup mount, so the file is not there.

## Changes

### 1. `.claude/plans/vps-setup.md` (requested)
- Structure tree: add
  ```
  ├── backups/                   # backup scripts (*.sh) + backup.log
  │   └── dump/                  # archived DB dumps (*.sql.gz), kept 7 days
  ```
- Item 9: `mkdir -p /home/dad/workspace/{infrastructure/letsencrypt,www,backups/dump}`.
- Item 17: script `/home/dad/workspace/backups/backup-vacancy-grabber.sh`, dumps to `/home/dad/workspace/backups/dump/`, cron log `/home/dad/workspace/backups/backup.log`, rotation 7 days in `dump/`.

### 2. `docs/vps-setup.md` (keep consistent with the plan)
- Directory tree under "Directory structure on the server": add `backups/` and `backups/dump/` with the same comments.
- Step 17, restore block:
  - `scp dump.sql.gz dad@100.91.57.78:/home/dad/workspace/backups/dump/`
  - `gunzip -c /home/dad/workspace/backups/dump/dump.sql.gz | docker compose ... exec -T mysql ...` (piped variant, unchanged otherwise)
  - Fix the interactive `SOURCE` variant: copy the file into the container first.
    ```bash
    gunzip -k /home/dad/workspace/backups/dump/dump.sql.gz
    docker compose -f docker-compose.prod.yaml --env-file .env.prod.local cp \
      /home/dad/workspace/backups/dump/dump.sql mysql:/tmp/dump.sql
    docker compose -f docker-compose.prod.yaml --env-file .env.prod.local exec -it mysql mysql -uroot -p
    # mysql> USE vacancy_grabber;
    # mysql> SOURCE /tmp/dump.sql;
    ```
    Replace `cat .env.prod.local` with `grep MYSQL_ROOT_PASSWORD .env.prod.local`, so all secrets are not printed.
  - The restore from an automatic backup example uses `ls -lh /home/dad/workspace/backups/dump` to pick the file.
- The redundant `touch` before `cat >` in step 17 is harmless. It stays as the user wrote it.

## Files
- `.claude/plans/vps-setup.md`
- `docs/vps-setup.md` (directory tree, step 17 restore block)

## Verification
- `grep -n "/home/dad/backups\|backups/dump.sql\|/var/www/html/file.sql" docs/vps-setup.md .claude/plans/vps-setup.md` returns nothing.
- `bash -n` on the backup script extracted from step 17 passes.
