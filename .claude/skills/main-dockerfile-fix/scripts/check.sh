#!/usr/bin/env bash
# Checks the main (production) Dockerfile against the current project. Read-only.
# Usage: check.sh [project-root]   (default: .)
# Output: "RULE  location  message" per finding, then a per-rule summary.
#         INFO lines need a manual look and do not fail the check.
# Exit: 0 = clean, 1 = findings, 2 = bad project root.
# Keep the rule table in ../SKILL.md in sync with this file.

set -u

ROOT="${1:-.}"
cd "$ROOT" 2>/dev/null || { echo "error: cannot enter $ROOT" >&2; exit 2; }

DF="Dockerfile"
if [[ ! -f "$DF" || ! -f composer.json ]]; then
    echo "error: $DF or composer.json not found in $(pwd) (run from the repo root or pass it)" >&2
    exit 2
fi

RULES=(F1 F2 F3 F4 F5 F6 F7 F8)
declare -A COUNT
declare -A TITLE=(
    [F1]="PHP version does not match composer.json / compose"
    [F2]="PHP extension missing in the image"
    [F3]="Node image does not match the dev node image"
    [F4]="COPY source does not exist"
    [F5]="Stage / compose target mismatch"
    [F6]=".dockerignore does not exclude a heavy or secret path"
    [F7]="Build hygiene (root user, dev packages, APP_ENV)"
    [F8]="Frontend build input missing in the assets stage"
)
TOTAL=0

report() { # rule location message
    printf '%-3s  %s  %s\n' "$1" "$2" "$3"
    COUNT[$1]=$(( ${COUNT[$1]:-0} + 1 ))
    TOTAL=$(( TOTAL + 1 ))
}
info() { printf 'INFO %s  %s\n' "$1" "$2"; }

# Dockerfile without comment lines, with line numbers: "N:content"
DF_BODY="$(grep -n -v '^[[:space:]]*#' "$DF")"

# ---------------------------------------------------------------- F1
df_php="$(sed -n 's/^ARG PHP_VERSION=\([0-9.]*\).*/\1/p' "$DF" | head -1)"
req_php="$(sed -n 's/.*"php": *"[^0-9]*\([0-9]\+\(\.[0-9]\+\)\?\).*/\1/p' composer.json | head -1)"
if [[ -z "$df_php" ]]; then
    report F1 "$DF" "no 'ARG PHP_VERSION=<x.y>' found"
elif [[ -n "$req_php" ]] && [[ "$(printf '%s\n%s\n' "$req_php" "$df_php" | sort -V | head -1)" != "$req_php" ]]; then
    report F1 "$DF" "PHP $df_php is lower than composer.json require.php ($req_php)"
fi
if [[ -n "$df_php" && -f docker-compose.yaml ]]; then
    dev_php="$(sed -n 's/.*PHP_VERSION:-\([0-9.]*\)}.*/\1/p' docker-compose.yaml | head -1)"
    [[ -n "$dev_php" && "$dev_php" != "$df_php" ]] \
        && report F1 "$DF" "PHP $df_php differs from the dev default $dev_php in docker-compose.yaml"
fi

# ---------------------------------------------------------------- F2
img_exts="$(grep -h 'install-php-extensions' "$DF" | grep -v '^[[:space:]]*#' \
    | sed 's/.*install-php-extensions//; s/\\//g; s/&&.*//' | tr -s ' ' '\n' | sed '/^$/d')"
# present in the official php image (no need to install)
builtin=" ctype curl date dom fileinfo filter hash iconv json libxml mbstring openssl pcre pdo pdo_sqlite phar posix random readline reflection session simplexml sodium spl sqlite3 standard tokenizer xml xmlreader xmlwriter zlib "
# true when an uncommented line of the env files contains the pattern
env_uses() { cat .env .env.prod.dist 2>/dev/null | grep -v '^[[:space:]]*#' | grep -q "$1"; }
has_ext() { grep -qx "$1" <<<"$img_exts" || [[ "$builtin" == *" $1 "* ]]; }

# only hard requirements: root "require" and the "require" of installed packages (not suggest/conflict/require-dev)
need="$(python3 -I - <<'PY'
import json
out = set()
d = json.load(open("composer.json"))
out |= {k for k in d.get("require", {}) if k.startswith("ext-")}
try:
    for pkg in json.load(open("composer.lock")).get("packages", []):
        out |= {k for k in pkg.get("require", {}) if k.startswith("ext-")}
except FileNotFoundError:
    pass
print(" ".join(sorted(k[4:] for k in out)))
PY
)"
for e in $(printf '%s\n' $need | sort -u); do
    has_ext "$e" || report F2 "composer.json" "ext-$e is required but not installed in the image"
done

# what the dev image installs, minus dev-only tooling
if [[ -f .docker/php-fpm/Dockerfile ]]; then
    dev_exts="$(grep -h 'install-php-extensions' .docker/php-fpm/Dockerfile \
        | grep -v '^[[:space:]]*\(ADD\|COPY\)' \
        | sed 's/.*install-php-extensions//; s/\\//g; s/&&.*//' | tr -s ' ' '\n' | sed '/^$/d')"
    for e in $dev_exts; do
        case "$e" in @composer|excimer|xdebug|pcov) continue ;; esac
        has_ext "$e" && continue
        # transport / DB extensions are needed only when a DSN uses them
        case "$e" in
            amqp)      env_uses 'amqp://' || { info F2 "$e is in the dev image only; no amqp:// DSN, skipped"; continue; } ;;
            redis)     env_uses 'redis://' || { info F2 "$e is in the dev image only; no redis:// DSN, skipped"; continue; } ;;
            pdo_pgsql) env_uses 'postgres' || { info F2 "$e is in the dev image only; no postgres DSN, skipped"; continue; } ;;
        esac
        report F2 "$DF" "extension '$e' is installed in the dev image but not in the production image"
    done
fi
env_uses 'mysql' && ! has_ext pdo_mysql && report F2 "$DF" "DATABASE_URL uses mysql but pdo_mysql is not installed"

# ---------------------------------------------------------------- F3
df_node="$(sed -n 's/^FROM \(node:[^ ]*\).*/\1/p' "$DF" | head -1)"
dev_node="$(sed -n 's/^FROM \(node:[^ ]*\).*/\1/p' .docker/nodejs/Dockerfile 2>/dev/null | head -1)"
if [[ -n "$df_node" && -n "$dev_node" && "$df_node" != "$dev_node" ]]; then
    report F3 "$DF" "$df_node differs from the dev image $dev_node (.docker/nodejs/Dockerfile)"
fi
[[ -z "$df_node" ]] && info F3 "no node stage found in $DF, frontend is built elsewhere?"

# ---------------------------------------------------------------- F4
while IFS= read -r line; do
    n="${line%%:*}"; body="${line#*:}"
    [[ "$body" =~ ^[[:space:]]*(COPY|ADD)[[:space:]] ]] || continue
    [[ "$body" == *"--from="* ]] && continue
    args="$(sed -E 's/^[[:space:]]*(COPY|ADD)[[:space:]]+//; s/--[a-z]+=[^ ]+[[:space:]]*//g' <<<"$body")"
    read -ra parts <<<"$args"
    (( ${#parts[@]} < 2 )) && continue
    for src in "${parts[@]:0:${#parts[@]}-1}"; do
        [[ "$src" == http* || "$src" == "." || "$src" == "./" ]] && continue
        src="${src#./}"
        compgen -G "$src" >/dev/null || [[ -e "$src" ]] || report F4 "$DF:$n" "'$src' does not exist"
    done
done <<<"$DF_BODY"

# ---------------------------------------------------------------- F5
stages="$(sed -n 's/^FROM .* AS \([A-Za-z0-9_-]*\)$/\1/Ip' "$DF")"
targets=""
for f in docker-compose*.y*ml; do
    [[ -f $f ]] && targets+=" $(sed -n 's/^[[:space:]]*target:[[:space:]]*\([A-Za-z0-9_-]*\).*/\1/p' "$f" | tr '\n' ' ')"
done
for t in $(printf '%s\n' $targets | sort -u); do
    grep -qx "$t" <<<"$stages" || report F5 "docker-compose*.yaml" "target '$t' is not a stage in $DF"
done
for s in $stages; do
    used=0
    grep -qE "(--from=$s( |$)|^FROM $s( |$))" "$DF" && used=1
    [[ " $targets " == *" $s "* ]] && used=1
    grep -qsE "target[ =]+$s( |$)|--target[ =]$s" makefile Makefile docs/*.md && used=1
    (( used )) || info F5 "stage '$s' is not used by another stage, compose or makefile"
done

# ---------------------------------------------------------------- F6
if [[ ! -f .dockerignore ]]; then
    report F6 ".dockerignore" "file is missing: the whole working tree (vendor, node_modules, .git, secrets) goes into the build context"
else
    # a path counts as excluded when any .dockerignore line matches it as a glob (e.g. ".env.*.local", "config/jwt/*.pem")
    ignored() {
        local pat
        while IFS= read -r pat; do
            pat="${pat%$'\r'}"; pat="${pat#/}"; pat="${pat%/}"
            [[ -z "$pat" || "$pat" == \#* ]] && continue
            # shellcheck disable=SC2053
            [[ "$1" == $pat ]] && return 0
        done < .dockerignore
        return 1
    }
    for p in .git vendor node_modules var .env.local .env.prod.local config/jwt/private.pem; do
        ignored "$p" || report F6 ".dockerignore" "'$p' is not excluded"
    done
fi

# ---------------------------------------------------------------- F7
app_block="$(awk 'BEGIN{IGNORECASE=1} /^FROM .* AS app$/{f=1;next} /^FROM /{f=0} f' "$DF")"
if [[ -n "$app_block" ]]; then
    last_user="$(grep -E '^USER ' <<<"$app_block" | tail -1 | awk '{print $2}')"
    if [[ -z "$last_user" || "$last_user" == root* || "$last_user" == 0* ]]; then
        report F7 "$DF" "the 'app' stage runs as root (add 'USER app:app' after the COPY steps)"
    fi
    grep -q 'APP_ENV=prod' <<<"$app_block" || report F7 "$DF" "the 'app' stage does not set APP_ENV=prod"
else
    report F5 "$DF" "no 'app' stage found"
fi
grep -q 'composer install' "$DF" && ! grep 'composer install' "$DF" | grep -q -- '--no-dev' \
    && report F7 "$DF" "composer install without --no-dev puts dev packages into the image"
grep -q 'dump-autoload' "$DF" || info F7 "no composer dump-autoload (an optimized autoloader is faster in prod)"

# ---------------------------------------------------------------- F8
assets_block="$(awk 'BEGIN{IGNORECASE=1} /^FROM .* AS assets$/{f=1;next} /^FROM /{f=0} f' "$DF")"
if [[ -n "$assets_block" ]]; then
    for f in package.json yarn.lock webpack.config.js tsconfig.json; do
        [[ -f $f ]] && ! grep -q "$f" <<<"$assets_block" && report F8 "$DF" "$f exists but is not copied into the assets stage"
    done
    [[ -d assets ]] && ! grep -qE 'COPY .*assets( |$)' <<<"$assets_block" && report F8 "$DF" "assets/ is not copied into the assets stage"
    grep -qs "\.env" webpack.config.js && ! grep -q '\.env' <<<"$assets_block" \
        && report F8 "$DF" "webpack.config.js reads .env but it is not copied into the assets stage"
    if [[ -d assets ]]; then
        for v in $(grep -rhoE "vendor/[A-Za-z0-9_.-]+" assets 2>/dev/null | sort -u); do
            grep -q "$v" <<<"$assets_block" || report F8 "assets/" "imports $v/ but the assets stage does not copy it from the vendor stage"
        done
    fi
else
    info F8 "no 'assets' stage found, skipped"
fi

# ---------------------------------------------------------------- summary
echo
echo "Summary:"
for r in "${RULES[@]}"; do
    printf '  %-4s %3s  %s\n' "$r" "${COUNT[$r]:-0}" "${TITLE[$r]}"
done
printf '  %-4s %3s\n' ALL "$TOTAL"

(( TOTAL == 0 ))
