# DDD structure check result

Date: 2026-10-14 · Branch: main · Commit: 0f35345 (Merged in ddd-layer-rules, pull request #2), no uncommitted changes
Command: `bash .claude/skills/ddd-structure-fix/scripts/check.sh` · Exit code: 0

## Summary

| Rule | Description | Violations |
|---|---|---|
| D1 | Domain depends on Application/Infrastructure | 0 |
| I1 | Doctrine adapter depends on Application | 0 |
| P1 | Inner layer depends on Presentation | 0 |
| D2 | Domain depends on a framework (Doctrine ORM/DBAL, Symfony, Gesdinet) | 0 |
| D3 | ORM attributes in Domain | 0 |
| D4 | Domain depends on legacy `App\Contract` | 0 |
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

The script only greps imports and file names, so a clean result does not cover the following. Review them by hand.

1. **Commented-out dead code (cosmetic).** No live code references a missing class. Delete what's left instead of keeping comments (all in `src/Presentation`):
   - the unused `src/Presentation/Resources/templates/admin/CRUD/list__link_entity_url.html.twig`
   - `src/Presentation/Shared/AdminTrait/ConfigureFormTrait.php`: `configureFormFieldElFinder()` (line 323) and `configureFormFieldLinkEntity()` (line 343)
   - `src/Presentation/Shared/AdminTrait/ConfigureListTrait.php`: `configureListFieldLinkEntity()` (line 161)
2. **Symfony in Application.** Handlers authorize through `Symfony\Bundle\SecurityBundle\Security`, and the voters live in `src/Application/Security/Voter`. This is accepted on purpose, but an `Authorizer` port would make Application framework-free. The check does not flag it.
3. **Framework metadata on Application DTOs.** `src/Application/Shared/DTO` and the module DTOs carry Serializer and OpenAPI attributes. This is acceptable; move the attributes to Presentation response models only if the API schema needs to diverge from the use-case output.
4. **Dependency checks in CI.** The rules are grep-based. Adding deptrac (or phparkitect) to CI would also catch inline class usage that has no `use` import. A check that every `'controller' => '…::method'` string in an admin resolves to a real class would have caught the batch-assign bug (see Resolved).

Item 1 is a cosmetic clean-up. Items 2–4 are design choices worth a decision. Batch "Assign" has no acceptance test; consider adding one.

Resolved:
- the `LocaleVO` comments were removed from `AbstractAdmin::trans()`. It calls the translator without a locale again, so admin labels follow the request locale. The temporary fixed `$locale` (`default_locale`) was dropped as well. `php -l`, `composer run lint` and the container lint pass, and the admin acceptance tests pass (7 tests, 43 assertions);
- the `generateJWT()` block in `AbstractAdmin` and the `LocaleVO` comments in `AuthController` were deleted (comments only). Both files pass `php -l`, `composer run lint` is clean, and the container lints OK;
- the copied Vacancy batch actions were removed from `NoteAdminCrudController` and `GrabLogAdminCrudController`. Only `batchActionAssign()` is left, the stale TODO comments and unused imports are gone, no admin references the removed actions, `composer run lint` is clean, and the container lints OK;
- **bug fixed:** batch "Assign" in the Vacancy, Note and GrabLog admins pointed at controller classes that don't exist (`App\Application\<Module>\…`, already broken on `main`). The `controller` options now use `App\Presentation\<Module>\Controller\Admin\<Module>AdminCrudController::batchActionAssign`, and all three resolve through Symfony's `controller_resolver` (checked in the test kernel);
- the unused leftover config in `src/Application/Service/SourceGrabber/Resources/` has been deleted;
- the dead `src/Presentation/Shared/AdminTrait/ConfigurePermissionSectionFieldTrait.php` has been deleted;
- `ConfigureFormFieldAssignedToUserTrait` no longer has the unused `configureFormFieldElFinder` and `configureFormFieldLinkEntity` methods. They had no callers, and the file passes `php -l`.
- the unused `LinkEntityInterface`/`ElFinderType` imports were removed from `ConfigureFormTrait`;
- `AbstractAdmin::generateJWT()` was disabled. It had no callers (PHP or Twig), and the `$jwtTokenManager` property it used was never declared, so the method could not have worked;
- `ConfigureFormTrait::configureFormFieldElFinder()`, `ConfigureFormTrait::configureFormFieldLinkEntity()` and `ConfigureListTrait::configureListFieldLinkEntity()` were disabled (commented out, see item 1). All edited files pass `php -l`, and the container lints OK;
- `NoteAdminCrudController` was trimmed to the `assign` batch action, and `NoteAdmin` re-enabled the `vacancy` filter and the `company-virtual` list column. Its template `@note_templates/admin/CRUD/list__company.html.twig` exists, and both files pass `php -l`;
- the body of `list__link_entity_url.html.twig` was commented out, and `lint:twig` now passes (it used to fail on `getLinkEntityUrl`);
- the admin traits now hard-code the `'admin-messages'` translation domain. This is equivalent to before, because no admin overrides the default `sonata_admin.default_translation_domain: 'admin-messages'`. The container lints OK.
