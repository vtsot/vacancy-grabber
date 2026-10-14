---
name: ddd-migration
description: Add or move a module (entity, repository, use cases, admin, API) into the layered src/{Domain,Application,Infrastructure,Presentation} DDD structure. Use when asked to migrate, move, port or add a component/module (Source, Vacancy, Dashboard, …) to DDD, or to plan such a move.
---

# DDD module layout: `src`

All legacy `src/Component/*` code has been migrated; there is no `src/Component`, `src/Contract` or `src/Controller` any more. Use this skill to add a new module or reshape an existing one. **GrabLog** is the cleanest end-to-end example; **Vacancy** shows the richest domain model (value objects, intent methods, notes). Check the result with the `ddd-structure-check` skill — it must report 0 violations.

Layer rules: **Domain → nothing** (only its own code, `Doctrine\Common\Collections`, `Symfony\Component\Uid`, Carbon). **Application → Domain**. **Infrastructure → Application + Domain**. **Presentation** (HTTP, Sonata Admin, Twig) is outermost: may use every layer, nothing imports it.

## Target layout

```
src/
├── Domain/<Module>/
│   ├── Entity/<Entity>.php             # plain PHP, no #[ORM\…], no setters (see "Entities")
│   ├── Repository/<Entity>Repository.php   # port; domain types only (no QueryBuilder/WindowPage)
│   ├── Repository/<Entity>Criteria.php     # readonly, all-nullable filter object for getAllPaginated()
│   ├── ValueObject/                    # e.g. Vacancy/ValueObject/{Salary,ExternalId}
│   └── Service/                        # domain services, e.g. Source/Service/VacancyImporter
├── Domain/Shared/{AbstractEntity, EntityInterface, EntityTrait/TimeStampableEntityTrait,
│                  Repository/{FindOneById,FindOneByIdAndUserId,FindOneByIdentifier,FindAllForUserId,PaginatedResult},
│                  ValueObject/Url, Exception/{DomainException,EntityNotFoundException,InvalidArgumentException}}
├── Application/<Module>/
│   ├── DTO/  DTOFactory/ (entity → output DTO; value objects → ->toString())
│   ├── Message/{Command,Query}/  MessageHandler/{Command,Query}/
│   └── Service/                        # use-case helpers, e.g. Vacancy/Service/VacancyFieldWriter
├── Application/Security/Voter/         # authorization used by handlers
├── Application/Shared/{Bus (handler/message interfaces), DTO, Message, Validation}
├── Application/Exception/              # AppException, AccessDeniedException, ValidationFailedException
├── Infrastructure/
│   ├── Doctrine/Mapping/<Module>.Entity.<Entity>.orm.xml     # file name = class path under App\Domain
│   ├── Doctrine/Repository/<Entity>DoctrineRepository.php     # + Repository/Trait/* (pagination, search, order-by, save/remove)
│   ├── Doctrine/Type/                  # DBAL types for value objects (url, salary, external_id)
│   ├── Doctrine/SecurityMapping/       # mappings of Infrastructure entities (RefreshToken)
│   ├── Security/                       # SecurityUser adapter, UserProvider, UserChecker, password hasher, RefreshToken
│   ├── SourceGrabber/                  # Dou/Djinni adapters of Domain\Source\Grabber\Grabber
│   ├── Messenger/MessageBusHandler.php
│   └── Resources/config/{doctrine.yaml, services.yaml}
└── Presentation/<Module>/
    ├── Admin/  Controller/{Admin,Api,Web}/  ControllerRequest/Api/
    ├── Factory/<Entity>CriteriaFactory.php                    # request → Domain criteria
    ├── EventSubscriber/  Resources/{config/{admin,services,translation,twig}.yaml, templates/, translations/}
    └── (Shared/: ApiDocTag, Admin base + EntityFieldMap + IntentPropertyAccessor, ArgumentResolver,
         ControllerRequest*, EventSubscriber, Exception, Security/Authenticator, Serializer, Twig, Validator, Util)
```

## Reference files to copy from

| Piece | Copy from |
|---|---|
| Entity (simple / rich) | `Domain/GrabLog/Entity/GrabLog.php` / `Domain/Vacancy/Entity/Vacancy.php` |
| Repo port + criteria | `Domain/GrabLog/Repository/GrabLogRepository.php`, `GrabLogCriteria.php` |
| Value object + DBAL type | `Domain/Shared/ValueObject/Url.php`, `Infrastructure/Doctrine/Type/UrlType.php` |
| XML mapping | `Infrastructure/Doctrine/Mapping/GrabLog.Entity.GrabLog.orm.xml`, `Note.Entity.Note.orm.xml` |
| Doctrine repo | `Infrastructure/Doctrine/Repository/GrabLogDoctrineRepository.php` |
| Criteria factory | `Presentation/GrabLog/Factory/GrabLogCriteriaFactory.php` |
| Query / handler / API controller | `Application/GrabLog/Message/Query/…`, `Application/GrabLog/MessageHandler/Query/…`, `Presentation/GrabLog/Controller/Api/GetCollectionController.php` |
| Command handler | `Application/Source/MessageHandler/Command/CreateSourceHandler.php` |
| Voter | `Application/Security/Voter/CompanyVoter.php` |
| Module config | `Presentation/GrabLog/Resources/config/*.yaml` |

## Entities

- No `set*` methods. Creation through named constructors (`Vacancy::create()`, `Vacancy::grabbed()`, `Source::create()`, `GrabLog::record()`, `User::register()`); changes through intent methods (`rename`, `changeUrl`, `markResumeSent`, `assignTo`, `activate`, `changePassword(plain, PasswordHasher)`…). Keep a **no-argument constructor** that initialises collections — Sonata instantiates entities with it.
- Business rules live in the entity (e.g. a new resume date, deadline or note marks a vacancy viewed) or in a domain service (`VacancyImporter`), not in handlers.
- Fields that need validation/normalisation are value objects (`Url`, `Salary`, `ExternalId`) stored through a DBAL type in the same VARCHAR column (no migration). VOs have `fromString()` (validates, throws `Domain\Shared\Exception\InvalidArgumentException`), `fromNullable()` (empty → null) and `fromStorage()` (persistence only).
- Every field Sonata Admin may edit must be listed in `Presentation/Shared/Admin/EntityFieldMap` (writer → intent method; reader only for value objects shown as text). `IntentPropertyAccessor` (decorates `property_accessor`) uses it for forms, inline list edits and child-admin parent linking; `DomainFormAdminExtension` turns domain exceptions into form errors. Forgetting a field gives `NoSuchPropertyException … add a setter` on submit.
- Avoid entity method names that look like PropertyAccess adders/removers for a form field (`addNote`, `removeNote` for a `notes` field) unless they take exactly that one item — use `writeNote()` / `attachNote()` / `detachNote()`.

## Procedure

1. **Domain**: entity (+ value objects), repository port extending the `Domain\Shared\Repository\Find*` ports it needs, `save()`/`remove()`, `getAllPaginated(<Entity>Criteria): PaginatedResult`, plus custom finders typed with domain classes.
2. **XML mapping**: translate 1:1 — table, index names, column names, types (`type="url"` etc. for VOs), `nullable`, defaults (`<options><option name="default">0</option></options>`), `enum-type`, `on-delete`, `inversed-by`/`mapped-by` (both sides). Copy id + `createdAt`/`updatedAt` (gedmo timestampable) blocks from an existing mapping. `repository-class` = the Doctrine repository.
3. **Doctrine repo**: implement the port; use the `Infrastructure/Doctrine/Repository/Trait/*` traits (`FindByIdTrait`, `FindByIdAndUserIdTrait`, `PaginatorRepositoryTrait::paginate()`, `QueryConditionBuilderRepositoryTrait`, `OrderByRepositoryTrait`, `SaveRemoveRepositoryTrait`); keep `createBaseQuery(<Entity>Criteria)` private. Only search fields that exist on the entity.
4. **Bind** the port in `Infrastructure/Resources/config/services.yaml`:
   ```yaml
   App\Domain\<Module>\Repository\<Entity>Repository:
       class: App\Infrastructure\Doctrine\Repository\<Entity>DoctrineRepository
   ```
5. **Application**: query/command + handler. Handlers depend on repository ports only — never `EntityManagerInterface`; load related entities through their repositories (`$userRepository->findOneById()`), persist with `$repository->save()`. Authorization via `Security::isGranted(<Voter>::…)`; not-found → `Domain\Shared\Exception\EntityNotFoundException`.
6. **Presentation**: request object, `<Entity>CriteriaFactory::fromCollectionRequest(?SecurityUser, GetCollectionRequest)` (trimming, `Util::normalizeDate(...)->startOfDay()/endOfDay()`, `Util::normalizeBool`, sort whitelist `Query::SORT_BY[$request->orderBy ?? ''] ?? array_values(Query::SORT_BY)[0]`), controller with `#[CurrentUser] SecurityUser $user`, admin (+ `EntityFieldMap` entries).
7. **Module config**: `Presentation/<Module>/Resources/config/services.yaml` imports only `translation.yaml` + `twig.yaml`; import it from `Presentation/Resources/config/services.yaml` (alphabetical); admin config from `config/admin.yaml`. Keep twig namespace (`@<module>_templates`), admin service id and route pattern stable.
8. **Format**: `docker exec vacancy-grabberlocal-php-fpm-1 composer run lint:fix`.

## Verification

All commands run in the container: `D="docker exec vacancy-grabberlocal-php-fpm-1"` (workdir `/var/www/html`).

```bash
bash .claude/skills/ddd-structure-check/scripts/check.sh   # host; must print ALL 0
$D bin/console cache:clear
$D bin/console lint:container
$D bin/console doctrine:mapping:info
$D bin/console doctrine:schema:validate                    # mapping + DB in sync
$D bin/console doctrine:schema:update --dump-sql            # expect nothing
$D composer run lint
$D composer run test                                        # api, web, unit (acceptance skipped)
$D php -d pcov.enabled=0 vendor/bin/codecept run acceptance # browser suite incl. Admin/Form/AdminFormCest (needs `docker compose up -d selenium nginx`)
```

## Known gotchas

- Column parity: `type="text"` with **no** `length` is MySQL `longtext`; `length="65535"` silently downgrades to TEXT. JSON/TEXT/BLOB columns can't have literal defaults (ERROR 1101) — a mapping that breaks `schema:create` breaks the test DB reset of the acceptance suite.
- The test DB (`vacancy_grabber_test`) is built by `make migrate` (`--env=test`); the acceptance suite drops and recreates its schema. If API tests fail with `TableNotFoundException … users`, report it and ask before rebuilding.
- API tests that log in fail with `JWTEncodeFailureException … (private key/passphrase)` when `config/jwt/*.pem` and `JWT_PASSPHRASE` disagree — a local env issue, not code.
- Symfony `DateType` submits mutable `DateTime`; `EntityFieldMap` converts to `DateTimeImmutable` — entity methods take immutable dates only.
- Sonata's own child-admin parent linking uses a fresh PropertyAccessor; `Presentation/Shared/Admin/AbstractAdmin::createNewInstance()` replaces it — keep child admins on that base class.
- `GrabLog` / `Note` admin CRUD controllers contain copy-pasted Vacancy batch actions (TODO in the files) — don't expand scope.
- phpstan is not installed. Moved classes that lose a same-namespace sibling reference are only caught at runtime: after moves, run every changed file through the test suites, or a php-parser based "unresolved class" scan.

## Report back

Summarize: files moved/added, behaviour changes or bugs fixed along the way, verification results with exact failures, and which failures are pre-existing vs. introduced. Don't commit unless asked.
