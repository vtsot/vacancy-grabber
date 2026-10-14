---
name: ddd-structure-check
description: Check that src follows the DDD layer rules (Domain/Application/Infrastructure dependency direction, framework-free Domain, repository and mapping pairing, no legacy folders). Use when asked to check, validate, verify or audit the DDD structure, layer rules or architecture, or to measure refactor progress.
---

# DDD structure check

Layer rules: **Domain → nothing** (only its own code plus `Doctrine\Common\Collections` and `Symfony\Component\Uid`). **Application → Domain**. **Infrastructure → Application + Domain**. **Presentation** (controllers, request objects, Sonata Admin, Twig, templates) is the outermost layer: it may use all the others, and nothing may import it.

## Run

```bash
bash .claude/skills/ddd-structure-check/scripts/check.sh        # scans src/
bash .claude/skills/ddd-structure-check/scripts/check.sh path/to/src
```

Each violation is printed on one line as `RULE  path:line  message`, followed by a per-rule summary. The exit code is `0` when clean, `1` when there are violations and `2` when the scan root is bad. There is no baseline: every violation fails the check. Runs on the host, no container needed.

## Rules

| ID | Rule | Fix (see `recomendation.md`) |
|---|---|---|
| D1 | Domain must not depend on Application/Infrastructure | #1: criteria objects become Domain types |
| D2 | Domain must be framework-free (no Doctrine ORM/DBAL, Symfony except Uid, Gesdinet) | #2, #3: domain-only repository signatures; move `RepositoryTrait/Doctrine` to Infrastructure |
| D3 | No `#[ORM\…]` attributes in Domain | #2: mapping stays in XML |
| D4 | Domain must not depend on legacy `App\Contract` | #5: split `Contract` by layer |
| I1 | `Infrastructure/Doctrine` must not depend on Application (repositories implement Domain ports only; other adapters may implement Application ports) | #4: criteria live in Domain |
| P1 | Domain, Application and Infrastructure must not import Presentation | move the shared piece inward, or the caller outward |
| A1 | Application must not use `EntityManagerInterface` / `QueryBuilder` | #6: `save()`/`remove()` on repositories, or a flusher port |
| A2 | DomCrawler/HttpClient adapters belong in Infrastructure | #8: move grabbers to `Infrastructure/SourceGrabber` |
| N1 | `Domain/<M>/Repository/` holds only `<E>Repository` (no `Interface` suffix) and `<E>Criteria`; each `<E>Repository` has `Infrastructure/Doctrine/Repository/<E>DoctrineRepository` | create or rename |
| N2 | Every non-enum `Domain/<M>/Entity/<E>` has `Infrastructure/Doctrine/Mapping/<M>.Entity.<E>.orm.xml` | add the mapping |
| L1 | No PHP left in `src/Component`, `src/Contract`, `src/Controller`, `src/VacancyGrabber` | #9: fold into the layers |

## Reporting

1. Run the script and read its whole output.
2. Summarise by rule, most severe first (D1, I1, P1, D2, D3, D4, A1, A2, N1, N2, L1). For each rule give the count, group the files by module, and name the fix from the table.
3. If there are many hits, show representative `path:line` references rather than listing every one. Give the totals from the summary.
4. If a hit is a false positive (e.g. a match inside a comment), say so instead of counting it.

This skill only reports. Do not edit code as part of the check. For moving files between layers, use the `ddd-migration` skill.

When a rule changes, update this table and `scripts/check.sh` together.
