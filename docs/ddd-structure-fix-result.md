# DDD structure check result

Date: 2026-11-12 · Branch: main · Commit: 4861895, uncommitted changes
Command: `bash .claude/skills/ddd-structure-fix/scripts/check.sh` · Exit code: 0

## Summary

| Rule | Description | Violations |
|---|---|---|
| D1 | Domain depends on Application/Infrastructure | 0 |
| D2 | Domain depends on a framework (Doctrine ORM/DBAL, Symfony, Gesdinet) | 0 |
| D3 | ORM attributes in Domain | 0 |
| D4 | Domain depends on legacy App\Contract | 0 |
| I1 | Doctrine adapter depends on Application | 0 |
| P1 | Inner layer depends on Presentation | 0 |
| A1 | Application uses the ORM directly | 0 |
| A2 | External adapter (DomCrawler/HttpClient) in Application | 0 |
| N1 | Repository naming/pairing | 0 |
| N2 | Entity without XML mapping | 0 |
| L1 | File in legacy location | 0 |
| **Total** | | **0** |

## Violations and recommended fixes

No violations.

## False positives

None.

## Recommendations

1. Nothing to fix. The structure follows the layer rules.
2. The script only greps imports, so it cannot see fully-qualified class names used without a `use`, dependencies injected via service config (`services.yaml`), or behavioural coupling between layers. Worth a manual look when adding new cross-layer code.
3. The newest code is in Presentation (`ApiRequestLimiterSubscriber`, `LocaleSubscriber`, `ApiRequestDetector`, `ApiSiteLoginRateLimiter`). Presentation may use all layers, so these are fine, and nothing inner imports them.
4. Re-run this check after moving code between layers.
