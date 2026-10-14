#!/usr/bin/env bash
# DDD layer-rule check for src/VacancyGrabber. Read-only.
# Usage: check.sh [src-root]   (default: src)
# Output: "RULE  path:line  message" per hit, then a per-rule summary.
# Exit: 0 = clean, 1 = violations found, 2 = bad scan root.
# Keep the rule table in ../SKILL.md in sync with this file.

set -u

ROOT="${1:-src}"
VG="$ROOT/VacancyGrabber"

if [[ ! -d "$VG" ]]; then
    echo "error: $VG not found (run from the repo root or pass the src dir)" >&2
    exit 2
fi

RULES=(D1 D2 D3 D4 I1 P1 A1 A2 N1 N2 L1)
declare -A COUNT
declare -A TITLE=(
    [D1]="Domain depends on Application/Infrastructure"
    [D2]="Domain depends on a framework (Doctrine ORM/DBAL, Symfony, Gesdinet)"
    [D3]="ORM attributes in Domain"
    [D4]="Domain depends on legacy App\\Contract"
    [I1]="Doctrine adapter depends on Application"
    [P1]="Inner layer depends on Presentation"
    [A1]="Application uses the ORM directly"
    [A2]="External adapter (DomCrawler/HttpClient) in Application"
    [N1]="Repository naming/pairing"
    [N2]="Entity without XML mapping"
    [L1]="File in legacy location"
)
TOTAL=0

report() { # rule location message
    printf '%-3s  %s  %s\n' "$1" "$2" "$3"
    COUNT[$1]=$(( ${COUNT[$1]:-0} + 1 ))
    TOTAL=$(( TOTAL + 1 ))
}

scan() { # rule dir pattern [exclude-pattern]
    local rule=$1 dir=$2 pattern=$3 exclude=${4:-} line file rest ln text
    [[ -d "$dir" ]] || return 0
    while IFS= read -r line; do
        file=${line%%:*}
        rest=${line#*:}
        ln=${rest%%:*}
        text=${rest#*:}
        text=${text#"${text%%[![:space:]]*}"}
        report "$rule" "$file:$ln" "$text"
    done < <(
        grep -rnE --include='*.php' -- "$pattern" "$dir" \
            | if [[ -n "$exclude" ]]; then grep -vE -- "$exclude"; else cat; fi \
            | sort
    )
}

# Patterns match `use` lines and inline FQCNs alike.
scan D1 "$VG/Domain"         'App\\VacancyGrabber\\(Application|Infrastructure)\\'
scan D2 "$VG/Domain"         '(^|[^A-Za-z0-9_\\])(Doctrine\\(ORM|DBAL)|Symfony|Gesdinet)\\' 'Symfony\\Component\\Uid\\'
scan D3 "$VG/Domain"         '#\[ORM\\'
scan D4 "$VG/Domain"         'App\\Contract\\'
scan I1 "$VG/Infrastructure/Doctrine" 'App\\VacancyGrabber\\Application\\'
for layer in Domain Application Infrastructure; do
    scan P1 "$VG/$layer"     'App\\VacancyGrabber\\Presentation\\'
done
scan A1 "$VG/Application"    'Doctrine\\ORM\\(EntityManagerInterface|QueryBuilder)([^A-Za-z0-9_]|$)'
scan A2 "$VG/Application"    'Symfony\\(Component\\(DomCrawler|HttpClient)|Contracts\\HttpClient)\\'

# N1: Domain/<M>/Repository/<E>Repository.php needs Infrastructure/Doctrine/Repository/<E>DoctrineRepository.php
# (Domain/Shared holds cross-module contracts, not an entity module)
for f in "$VG"/Domain/*/Repository/*.php; do
    [[ -e "$f" && "$f" != "$VG/Domain/Shared/"* ]] || continue
    name=$(basename "$f" .php)
    if [[ "$name" == *Interface ]]; then
        report N1 "$f:1" "repository interface must not use the Interface suffix"
    elif [[ "$name" == *Repository ]]; then
        impl="$VG/Infrastructure/Doctrine/Repository/${name%Repository}DoctrineRepository.php"
        [[ -e "$impl" ]] || report N1 "$f:1" "no implementation at $impl"
    elif [[ "$name" != *Criteria ]]; then
        report N1 "$f:1" "file in Repository/ is not named <Entity>Repository or <Entity>Criteria"
    fi
done

# N2: every non-enum Domain/<M>/Entity/<E>.php needs Infrastructure/Doctrine/Mapping/<M>.Entity.<E>.orm.xml
for f in "$VG"/Domain/*/Entity/*.php; do
    [[ -e "$f" ]] || continue
    grep -qE '^enum ' "$f" && continue
    module=$(basename "$(dirname "$(dirname "$f")")")
    entity=$(basename "$f" .php)
    mapping="$VG/Infrastructure/Doctrine/Mapping/$module.Entity.$entity.orm.xml"
    [[ -e "$mapping" ]] || report N2 "$f:1" "no mapping at $mapping"
done

# L1: nothing may remain in the pre-DDD locations
while IFS= read -r f; do
    report L1 "$f:1" "move into src/VacancyGrabber/{Domain,Application,Infrastructure}"
done < <(find "$ROOT/Component" "$ROOT/Contract" "$ROOT/Controller" -name '*.php' 2>/dev/null | sort)

echo
echo "Summary:"
for r in "${RULES[@]}"; do
    printf '  %-3s %4d  %s\n' "$r" "${COUNT[$r]:-0}" "${TITLE[$r]}"
done
printf '  %-3s %4d\n' "ALL" "$TOTAL"

(( TOTAL == 0 ))
