---
name: ddd-migration
description: Move a legacy module from src/Component/<Module> into the layered src/VacancyGrabber/{Domain,Application,Infrastructure} DDD structure. Use when asked to migrate, move or port a component (Source, Vacancy, Dashboard, …) to DDD, or to plan such a move.
---

# DDD migration: `src/Component/<Module>` → `src/VacancyGrabber`

Already migrated (use them as references): **Company**, **Note**, **User**, **GrabLog**, **Source**, **Vacancy**, **Dashboard** — all Domain entities are now in DDD. Remaining legacy code: `src/Component/VacancyGrabber` (grabber services/DTOs), `src/Component/General` (shared base classes/services). Enums live next to their entity (`Domain/User/Entity/UserRole`, `Domain/Vacancy/Entity/VacancyStatus|VacancyGrabber`).
GrabLog is the cleanest end-to-end example (entity, mapping, repo, criteria flow, config).

## Target layout

```
src/VacancyGrabber/
├── Domain/<Module>/Entity/<Entity>.php                       # plain PHP, NO #[ORM\…] attributes
├── Domain/<Module>/Repository/<Entity>RepositoryInterface.php
├── Domain/Shared/{AbstractEntity, EntityTrait/*, RepositoryTrait/Doctrine/*}
├── Application/<Module>/
│   ├── Admin/  Controller/{Admin,Api,Web}/  ControllerRequest/Api/
│   ├── DTO/ (incl. <Entity>CriteriaDTO)  DTOFactory/ (incl. <Entity>CriteriaDTOFactory)
│   ├── Message/Query/  MessageHandler/Query/  Security/Voter/
│   └── Resources/{config/{admin,services,translation,twig}.yaml, templates/, translations/}
├── Application/Resources/config/services.yaml                # imports every module's services.yaml
└── Infrastructure/
    ├── Doctrine/Mapping/<Module>.Entity.<Entity>.orm.xml     # file name = class path under App\VacancyGrabber\Domain
    ├── Doctrine/Repository/<Entity>DoctrineRepository.php
    └── Resources/config/{doctrine.yaml, services.yaml}       # doctrine.yaml already maps the whole Mapping dir
```

## Reference files to copy from

| Piece | Copy from |
|---|---|
| Entity | `src/VacancyGrabber/Domain/GrabLog/Entity/GrabLog.php` |
| Repo interface | `src/VacancyGrabber/Domain/GrabLog/Repository/GrabLogRepositoryInterface.php` |
| XML mapping | `src/VacancyGrabber/Infrastructure/Doctrine/Mapping/GrabLog.Entity.GrabLog.orm.xml`, `Note.Entity.Note.orm.xml` |
| Doctrine repo | `src/VacancyGrabber/Infrastructure/Doctrine/Repository/GrabLogDoctrineRepository.php` |
| Criteria DTO + factory | `Application/GrabLog/DTO/GrabLogCriteriaDTO.php`, `DTOFactory/GrabLogCriteriaDTOFactory.php` |
| Query / handler / API controller | `Application/GrabLog/Message/Query/…`, `MessageHandler/Query/…`, `Controller/Api/GetCollectionController.php` |
| Voter | `Application/Company/Security/Voter/CompanyVoter.php` |
| Module config | `Application/GrabLog/Resources/config/*.yaml` |

## Modules without an entity (Application-only)

If the module has no `Entity/` content and no repository (e.g. Dashboard — read-only aggregate queries over other modules' repositories), move everything to `Application/<Module>/` only: no Domain, no XML mapping, no repo binding. `git rm` its `Entity/.gitkeep` and the `doctrine.yaml` that maps the empty dir. Queries that only carry `userId` don't need the criteria flow. Dead pieces (empty `admin.yaml` not imported anywhere, templates duplicated from another module) are moved as-is and reported, not deleted. Verify handler wiring with `bin/console debug:messenger query.bus | grep -i <module>`.

## Procedure

Before anything, grep every reference to the module outside its folder:
`grep -rn "Component\\\\<Module>\|<module_snake>" src config tests`

1. **Move with history.** `git mv` every file to its target path (entity → Domain, repository → `Infrastructure/Doctrine/Repository/<Entity>DoctrineRepository.php`, the rest → `Application/<Module>/`). `git rm` the old `Resources/config/doctrine.yaml`. Remove the empty `src/Component/<Module>`.
2. **Bulk rewrite namespaces** with `sed` over moved files + external references, in this order:
   - `App\Component\<Module>\Entity\X` → `App\VacancyGrabber\Domain\<Module>\Entity\X`
   - `App\Component\<Module>\Repository\XRepository` → `App\VacancyGrabber\Domain\<Module>\Repository\XRepositoryInterface`
   - `App\Component\<Module>\` → `App\VacancyGrabber\Application\<Module>\`
   - `src/Component/<Module>/` → `src/VacancyGrabber/Application/<Module>/` (yaml paths)
   - The `namespace App\Component\<Module>\Entity;` / `…\Repository;` / `…\Contract\…;` lines have **no trailing backslash**, so the generic rule turns them into `Application\…` — fix the namespace line of every file that went to Domain/Infrastructure explicitly, then `grep -rn '^namespace' Domain/<Module> Infrastructure` to confirm.
   - Also fix FQCN strings (Sonata batch `controller`, admin.yaml `class/model_class/controller`) and twig `{# @var … #}` comments.
3. **Domain entity**: strip all `#[ORM\…]` + `use Doctrine\ORM\Mapping`; extend `App\VacancyGrabber\Domain\Shared\AbstractEntity`; switch traits to `Domain\Shared\EntityTrait\*`; switch relations to Domain entities where they exist (User, Company, Note, GrabLog). A relation to a not-yet-migrated entity keeps the `App\Component\…` type with a `// todo: switch to the domain X once it is moved to ddd`.
4. **Repo interface**: `extends findOneById, findOneByIdAndUserId, Paginator, BuildQueryCondition` (+ `findAllForUserId` if used) with `createBaseQuery(<Entity>CriteriaDTO): QueryBuilder` and `getAllPaginated(<Entity>CriteriaDTO): WindowPage`. Add any custom finders the old repository exposed and that callers use.
5. **XML mapping**: translate attributes 1:1 — table name, index names, column names, types, `nullable`, defaults (`<options><option name="default">0</option></options>`), `enum-type`, `on-delete`, `inversed-by`/`mapped-by`. Copy id + `createdAt`/`updatedAt` (gedmo timestampable) blocks from an existing mapping. `repository-class` = the **interface**. Every bidirectional relation needs both sides (`inversed-by` ↔ `mapped-by`) — check the other entity.
6. **Doctrine repo**: implement the interface, keep `FindByIdTrait`, `FindByIdAndUserIdTrait`, `PaginatorRepositoryTrait`, `QueryConditionBuilderRepositoryTrait`; port the old `getBaseQuery(…args)` to `createBaseQuery(CriteriaDTO)`. Only search fields that actually exist on the entity.
7. **Bind** in `Infrastructure/Resources/config/services.yaml`:
   ```yaml
   App\VacancyGrabber\Domain\<Module>\Repository\<Entity>RepositoryInterface:
       class: App\VacancyGrabber\Infrastructure\Doctrine\Repository\<Entity>DoctrineRepository
   ```
8. **Criteria flow** (Application):
   - `<Entity>CriteriaDTO` — readonly, all-nullable: `userId, page, perPage, query, orderBy, orderDirection, …filters`.
   - `<Entity>CriteriaDTOFactory::fromCollectionRequest(?UserInterface, GetCollectionRequest)` — does trimming, `Util::normalizeDate(...)->startOfDay()/endOfDay()`, `Util::normalizeBool`, and **whitelists sort**: `Query::SORT_BY[$request->orderBy ?? ''] ?? array_values(Query::SORT_BY)[0]`.
   - Query: `final readonly class Get<Entity>CollectionQuery { FILTERS…, SORT_BY = ['front_key' => 'q.field'], __construct(public <Entity>CriteriaDTO $criteria) }` — no longer extends `AbstractGetCollectionQuery`.
   - Controller: inject the factory, `new Get<Entity>CollectionQuery($factory->fromCollectionRequest($user, $request))`. Keep route path/name, OA attributes, serializer groups unchanged.
   - Handler: inject the repo **interface**, `getAllPaginated($query->criteria)`, build meta from `$criteria` (`resolveFiltersOrderBy($criteria->orderBy, …)`).
   - Voter / Admin / DTOFactory: retype to Domain entity + repo interface. Admin still extends `App\Component\General\Shared\AbstractAdmin`.
9. **Module config**: `services.yaml` imports only `translation.yaml` + `twig.yaml` (no doctrine). Keep the twig namespace (`@<module>_templates`), admin service id and route pattern identical.
10. **Wiring**:
    - `config/services.yaml`: comment out the `src/Component/<Module>/…/services.yaml` import (same style as the others).
    - `config/admin.yaml`: point to `src/VacancyGrabber/Application/<Module>/Resources/config/admin.yaml`.
    - `src/VacancyGrabber/Application/Resources/config/services.yaml`: add `- { resource: ../../<Module>/Resources/config/services.yaml }` (alphabetical).
11. **External callers**: update imports in remaining `src/Component/*` code. Where a caller does `$em->getReference(LegacyX::class, …)` and passes it into a migrated entity setter, switch to the Domain class (alias `use … as DomainX;` if the legacy one is still needed in that file).
12. **Format**: `docker exec vacancy-grabberlocal-php-fpm-1 vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php --path-mode=intersection <changed .php files>`, then review the diff of files you didn't otherwise edit (should be import order only).

## Verification

All commands run in the container: `D="docker exec vacancy-grabberlocal-php-fpm-1"` (workdir `/var/www/html`).

```bash
$D bin/console cache:clear
$D bin/console lint:container
$D bin/console doctrine:mapping:info            # new entity [OK], no App\Component\<Module>
$D bin/console doctrine:schema:validate --skip-sync
$D bin/console doctrine:schema:update --dump-sql # expect no SQL for the module's tables
$D bin/console debug:router | grep <module>      # routes unchanged
grep -rn "Component\\\\<Module>" src config tests # empty
$D php -l <file>                                  # each changed file
$D vendor/bin/codecept run api <Module>s          # suite names are lowercase: api, web, unit, acceptance
```

## Known gotchas (state of the `ddd` branch)

- `doctrine:mapping:info`, `schema:validate` and `schema:update --dump-sql` run directly (no file needs moving aside any more). Baseline diff that is **expected** and not caused by a migration (indexes/defaults added deliberately in XML, no migration yet): `idx_name`/`uniq_id` on companies, `idx_created_at`/`idx_updated_at` on notes, `roles` default + `uniq_id` on users. Anything beyond that baseline is drift you introduced.
- Column parity: legacy `Types::TEXT` without length is MySQL `longtext` → in XML write `type="text"` with **no** `length` (`length="65535"` silently downgrades to TEXT). Copy `nullable` from the legacy trait (e.g. `UrlEntityTrait` url is nullable). 
- When API tests can't reach the code (JWT), smoke-test a rewritten repository by piping a PHP script into `docker exec -i -w /var/www/html vacancy-grabberlocal-php-fpm-1 php` that boots `App\Kernel('dev', true)` (**debug must be true** — a `debug=false` dev container is not rebuilt on change and fails with stale repository-service errors; run `bin/console cache:clear` first). Wrap any write in `$em->beginTransaction()` … `$em->rollback()` and calls each repo method read-only.
- API tests that log in fail with `JWTEncodeFailureException … (private key/passphrase)` — a local env issue (`config/jwt/*.pem` vs `JWT_PASSPHRASE`), not code. Unauthorized/validation tests still run and must pass.
- XML mappings must re-declare what attributes used to carry implicitly: `#[ORM\EntityListeners]` → `<entity-listeners><entity-listener class="…"/></entity-listeners>`, lifecycle callbacks, `OrderBy`. Doctrine-only classes (entity listeners) go to `Infrastructure/Doctrine/EntityListener/`; security adapters (UserProvider, Authenticator, UserChecker) to `Application/<Module>/Security/`; kernel subscribers to `Application/<Module>/EventSubscriber/`. Update FQCNs in `config/packages/security.yaml` too.
- A module can be "half migrated": Domain + Application exist but a legacy `src/Component/<Module>/Entity` is still imported by other components. Compare the legacy and Domain entities and port missing relations/methods that callers, admin queries (`->leftJoin($alias.'.x')`) or twig (`object.x`) still use before deleting the legacy class.
- `src/Component/VacancyGrabber/Resources/config/admin.yaml` is an unused duplicate of Source's admin config (not imported anywhere) — keep FQCNs in sync or delete it with the user's OK.
- `GrabLog` / `Note` admin CRUD controllers contain copy-pasted Vacancy batch actions — move as-is, add a cleanup TODO, don't expand scope.
- phpstan is not installed; php-cs-fixer needs `--config=.php-cs-fixer.php` when given multiple paths.

## Report back

Summarize: files moved, behaviour changes/bugs fixed along the way, verification results with exact failures, and which failures are pre-existing vs. introduced. Don't commit unless asked.
