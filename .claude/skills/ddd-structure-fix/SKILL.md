---
name: ddd-structure-fix
description: Check that src follows the DDD layer rules (Domain/Application/Infrastructure/Presentation dependency direction, framework-free Domain, repository and mapping pairing, no legacy folders) and write the result with fix recommendations to docs/ddd-structure-fix-result.md. Use when asked to check, validate, verify or audit the DDD structure, layer rules or architecture, to get DDD fix recommendations, or to measure refactor progress.
---

# DDD structure check and fix recommendations

Layer rules: **Domain → nothing** (only its own code plus `Doctrine\Common\Collections`, `Symfony\Component\Uid` and Carbon). **Application → Domain**. **Infrastructure → Application + Domain**. **Presentation** (controllers, request objects, Sonata Admin, Twig, templates) is the outermost layer: it may use all the others, and nothing may import it.

## Run

```bash
bash .claude/skills/ddd-structure-fix/scripts/check.sh        # scans src/
bash .claude/skills/ddd-structure-fix/scripts/check.sh path/to/src
```

Each violation is printed on one line as `RULE  path:line  message`, followed by a per-rule summary. The exit code is `0` when clean, `1` when there are violations and `2` when the scan root is bad. There is no baseline: every violation fails the check. Runs on the host, no container needed.

## Rules

| ID | Rule | How to fix |
|---|---|---|
| D1 | Domain must not depend on Application/Infrastructure | Move the referenced type into Domain (e.g. repository criteria live in `Domain/<M>/Repository/<E>Criteria`), or invert the dependency with a Domain interface (port) implemented outside. |
| D2 | Domain must be framework-free (no Doctrine ORM/DBAL, Symfony except Uid, Gesdinet) | Repository ports take and return domain types only (`PaginatedResult`, entities, value objects); Doctrine code goes to `Infrastructure/Doctrine/Repository` and its `Trait/`; framework-bound entities (e.g. bundle base classes) go to Infrastructure. |
| D3 | No `#[ORM\…]` attributes in Domain | Put the mapping in `Infrastructure/Doctrine/Mapping/*.orm.xml`. |
| D4 | Domain must not depend on legacy `App\Contract` | Split the contract by layer: entity/repository contracts to `Domain/Shared`, bus interfaces to `Application/Shared/Bus`, request contracts to Presentation. |
| I1 | `Infrastructure/Doctrine` must not depend on Application (repositories implement Domain ports only; other adapters may implement Application ports) | Use Domain criteria/types in repository signatures; move helpers the repository needs into an Infrastructure trait. |
| P1 | Domain, Application and Infrastructure must not import Presentation | Move the shared piece inward (e.g. validation used by handlers → `Application/Shared/Validation`), or move the caller outward into Presentation. |
| A1 | Application must not use `EntityManagerInterface` / `QueryBuilder` | Persist with `save()`/`remove()` on the repository port; load related entities through their repositories instead of `getReference()`. |
| A2 | DomCrawler/HttpClient adapters belong in Infrastructure | Define a Domain port (as `Domain/Source/Grabber/Grabber`) and implement it in `Infrastructure/<Adapter>/`. |
| N1 | `Domain/<M>/Repository/` holds only `<E>Repository` (no `Interface` suffix) and `<E>Criteria`; each `<E>Repository` has `Infrastructure/Doctrine/Repository/<E>DoctrineRepository` | Create the missing implementation or rename the file. |
| N2 | Every non-enum `Domain/<M>/Entity/<E>` has `Infrastructure/Doctrine/Mapping/<M>.Entity.<E>.orm.xml` | Add the mapping. |
| L1 | No PHP left in `src/Component`, `src/Contract`, `src/Controller`, `src/VacancyGrabber` | Fold the code into `src/{Domain,Application,Infrastructure,Presentation}`. |

## Procedure

1. Run the script and read its whole output.
2. Check each hit. A hit that is not a real dependency (e.g. a match inside a comment or a string) is a false positive: list it separately, don't count it.
3. Group the real violations by rule, most severe first (D1, I1, P1, D2, D3, D4, A1, A2, N1, N2, L1), and within a rule by module. For each rule write the concrete fix for these files, based on the "How to fix" column.
4. Write the result to `docs/ddd-structure-fix-result.md` (see below). Overwrite the file on every run: it always describes the current state.
5. In chat, give the totals, the most severe findings and the path to the result file.

This skill does not edit code; it reports and recommends. After moving code between layers, run this skill again.

## Result file: `docs/ddd-structure-fix-result.md`

Use this structure:

```markdown
# DDD structure check result

Date: <YYYY-MM-DD> · Branch: <git branch> · Commit: <short hash>[, uncommitted changes]
Command: `bash .claude/skills/ddd-structure-fix/scripts/check.sh` · Exit code: <0|1>

## Summary

| Rule | Description | Violations |
|---|---|---|
| D1 | … | <n> |
| … every rule, in the order above … |
| **Total** | | **<n>** |

## Violations and recommended fixes

### <ID>: <rule> (<n>)
- `path:line`: `<offending import>`
  (group by module; if there are many, list representative lines and give the count per module)

**Fix:** <concrete steps for these files>

(repeat per rule with violations; when there are none, write "No violations.")

## False positives

<hits that are not real dependencies, with the reason, or "None.">

## Recommendations

<ordered list of next steps, cheapest and most impactful first, and which fixes clear
which rules (e.g. "moving the criteria into Domain clears D1 and I1"). When the check is
clean, note what the script cannot see (it greps imports only) and anything worth
reviewing by hand.>
```

When a rule changes, update the Rules table, the result-file template and `scripts/check.sh` together.
