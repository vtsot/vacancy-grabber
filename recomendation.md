# DDD Structure Review — Recommendations

## Summary

The folder layout under `src/` is set up correctly, and moving the Doctrine mappings to XML in Infrastructure was the right call. But the layers still depend on each other in the wrong direction: Domain uses Application classes and Doctrine query types, and Application talks to Doctrine directly. So the folders follow DDD, but the dependencies between them don't yet.

## What's already right

- There are three layers (`Domain`, `Application`, `Infrastructure`) and each has the same sub-modules (Company, Vacancy, Source, …).
- Domain entities have no ORM attributes. The mapping lives in `Infrastructure/Doctrine/Mapping/*.orm.xml`.
- Each repository has an interface in Domain (`Domain/*/Repository/*Repository.php`) and its Doctrine implementation in `Infrastructure/Doctrine/Repository/`.
- Commands and queries are separate in Application (`Message/Command`, `Message/Query`, plus their handlers).

## Problems, most serious first

### 1. Domain depends on Application

All five repository interfaces import Application DTOs:

```php
// Domain/Vacancy/Repository/VacancyRepository.php
use App\VacancyGrabber\Application\Component\Vacancy\DTO\VacancyCriteriaDTO;
```

**Fix:** make the criteria objects Domain types, e.g. `Domain/Vacancy/Repository/VacancyCriteria.php`. Application can keep a factory that builds them from a request.

### 2. Repository interfaces expose Doctrine

`createBaseQuery(): QueryBuilder`, `getAllPaginated(): WindowPage`, plus the `Paginator` and `BuildQueryCondition` contracts and the `DoctrineRepository` marker. Any code using the interface is tied to the ORM.

**Fix:** Domain interfaces should take and return only domain types, e.g. `findPaginated(VacancyCriteria): PaginatedResult<Vacancy>` with your own small `PaginatedResult` class. `createBaseQuery` and `buildQueryCondition` become private details of the Doctrine repository.

### 3. Doctrine traits sit in Domain

`Domain/Shared/RepositoryTrait/Doctrine/*` are pure ORM code.

**Fix:** move them to `Infrastructure/Doctrine/Repository/Trait/`.

### 4. Infrastructure depends on Application

The Doctrine repositories import the Application criteria DTOs and `Application\Shared\Util`.

**Fix:** this goes away once #1 is fixed. Move `Util` to `Domain/Shared` or a framework-free `Shared` kernel.

### 5. `App\Contract` is outside the layers and mixes concerns

- `Contract/SourceGrabberInterface` imports Application DTOs and Symfony's `Crawler`, and Domain imports from `Contract`. So the dependencies form a cycle: Domain → Contract → Application.
- `Contract/Entity/UserInterface` extends Symfony Security interfaces, and Domain `User` implements it. As a result, the domain user carries security details: `plainPassword`, Google Authenticator token and `setRoles`.

**Fix:** split `Contract` by layer:

- Repository and Entity contracts go into `Domain/Shared`.
- Message and handler interfaces go into `Application/Shared/Bus`.
- The grabber becomes a port, `Domain/Source/SourceGrabber` (interface), with `grab(url): VacancyData[]`. `parseCompany(Crawler)` is an implementation detail and shouldn't be part of the interface.
- For security, add a `SecurityUser` adapter in Infrastructure that wraps the domain `User`, so `User` no longer implements Symfony interfaces.

### 6. Application calls `EntityManagerInterface` directly

About 15 command handlers do this, e.g. `GrabSourceHandler` calls `$this->em->persist/flush/getReference`.

**Fix:** add `save()`/`remove()` to the repository interfaces, or a small `TransactionManager`/`Flusher` port. Handlers would then depend only on Domain interfaces.

### 7. The domain model has no behaviour

Entities are getters and setters (`Vacancy` has `setStatus`, `setResumeSentAt`, …). The business rules live in handlers and `VacancyEntityFactory`, which is a common DDD shortcut but not real DDD.

**Fix:**

- Use named constructors and intent methods, e.g. `Vacancy::grabbed(...)`, `$vacancy->markResumeSent($at)`, `$vacancy->changeStatus(...)`, so invariants are checked inside the entity.
- `GrabSourceHandler` contains domain logic: dedupe by external id, create the company if missing, count results. Move that into a domain service such as `Domain/Source/Service/VacancyImporter`.
- Consider value objects for `Salary`, `Url` and `ExternalId` instead of plain-string traits.

### 8. Application is mixing two layers

Controllers, Sonata Admin classes, Twig extensions, templates, `ArgumentResolver` and EventSubscribers are UI/delivery code. Concrete grabbers (`DouSourceGrabber`, `DjinniSourceGrabber`, using HTTP and DomCrawler) are Infrastructure adapters.

**Fix:** either add a fourth layer, `Presentation` (or `UI`), for Controller, ControllerRequest, Admin, Twig, Resources/templates and EventSubscriber, or move them under Infrastructure. Move the grabbers to `Infrastructure/SourceGrabber/`.

### 9. Smaller points

- **Exceptions:** `EntityNotFoundException` and `InvalidArgumentException` belong in Domain. `AccessDeniedException` and the HTTP-shaped ones (`BadRequest`, `UnprocessableEntity`) belong in Presentation.
- **Mapping file names:** names like `Vacancy.Entity.Vacancy.orm.xml` depend on Doctrine's prefix config. That works, but grouping by context (`Mapping/Vacancy/Vacancy.orm.xml`) reads more clearly.
- **Dashboard:** it has no domain of its own; it's a read-model over Vacancy. That's fine, but say so explicitly: it should be queries only and never get a Domain folder.
- **Leftovers in the old locations:** `src/Controller/*` and `src/Contract/*` should be folded into the layers. `PatchOldRequest.php` looks like dead code.

## Suggested target layout

```
src/VacancyGrabber/
  Domain/<Ctx>/{Entity,ValueObject,Repository(interfaces),Service,Exception}
  Domain/Shared/
  Application/<Ctx>/{Command,Query,Handler,DTO}   + Application/Shared/Bus
  Infrastructure/{Doctrine/{Mapping,Repository,Type},SourceGrabber,Security}
  Presentation/<Ctx>/{Controller/{Api,Web,Admin},Request,Admin}, Twig, Resources
```

## Suggested order

1. Criteria DTOs into Domain (#1, #4).
2. Remove Doctrine types from the repository interfaces and move the traits (#2, #3).
3. Add `save`/`flush` to the repositories and remove `EntityManager` from the handlers (#6).
4. Break up `App\Contract` and add the security user adapter (#5).
5. Move the presentation and grabber code (#8).
6. Gradually add behaviour to the entities (#7).

Steps 1–3 are mechanical and low-risk.

## Keeping the layer rules enforced

Add **deptrac** (or `phparkitect`) to CI with these rules:

- Domain → nothing.
- Application → Domain.
- Infrastructure and Presentation → Application and Domain.

---

## Status: applied on branch `ddd-layer-rules`

`bash .claude/skills/ddd-structure-check/scripts/check.sh` now reports **0 violations** (was 129).

| # | Result |
|---|---|
| 1 | Done. Criteria objects are Domain types: `Domain/<Module>/Repository/<Entity>Criteria`. |
| 2 | Done. Repository ports use domain types only; pagination returns `Domain/Shared/Repository/PaginatedResult`. |
| 3 | Done. Doctrine traits live in `Infrastructure/Doctrine/Repository/Trait/`. |
| 4 | Done. Order-by normalisation moved into an Infrastructure trait; `Util` is in Presentation. |
| 5 | Done. `App\Contract` is gone: entity/finder contracts went to `Domain/Shared`, bus interfaces to `Application/Shared/Bus`, the grabber became the `Domain/Source/Grabber` ports, and request contracts went to Presentation. Domain `User` no longer implements Symfony interfaces; `Infrastructure/Security/SecurityUser` adapts it, and passwords are hashed through the `Domain/User/PasswordHasher` port. `RefreshToken` moved to Infrastructure. |
| 6 | Done. Handlers use `save()`/`remove()` on the repository ports; no `EntityManagerInterface` in Application. |
| 7 | Done (full). No setters on entities: named constructors and intent methods, `Url`/`Salary`/`ExternalId` value objects (DBAL types, same columns), and the `VacancyImporter` domain service for grabbing. Sonata Admin edits entities through `Presentation/Shared/Admin/EntityFieldMap`. Entities keep a no-argument constructor because Sonata instantiates them. |
| 8 | Done. New `Presentation` layer, and `Component\` dropped from namespaces. The grabbers are `Infrastructure/SourceGrabber` adapters. Rule P1 enforces that nothing imports Presentation. |
| 9 | Done, with two corrections. `AccessDeniedException` stays in Application, because the handlers enforce authorization; moving it to Presentation would invert the dependency. Mapping files keep the `<Module>.Entity.<Entity>.orm.xml` naming, which the check and the docs rely on. `PatchOldRequest` was deleted and `src/Controller` was folded into Presentation. |

Rule I1 was narrowed to `Infrastructure/Doctrine`: other adapters, such as Messenger, may implement Application ports.
