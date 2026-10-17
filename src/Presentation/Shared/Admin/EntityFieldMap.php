<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Admin;

use App\Domain\Company\Entity\Company;
use App\Domain\GrabLog\Entity\GrabLog;
use App\Domain\Note\Entity\Note;
use App\Domain\Shared\ValueObject\Url;
use App\Domain\Source\Entity\Source;
use App\Domain\User\Entity\User;
use App\Domain\Vacancy\Entity\Vacancy;
use App\Domain\Vacancy\ValueObject\ExternalId;
use App\Domain\Vacancy\ValueObject\Salary;
use Closure;
use DateTimeImmutable;
use DateTimeInterface;

use function in_array;

/**
 * Translates generic field edits coming from Sonata Admin (forms, inline list edits, child admins)
 * into the entities' intent methods. Entities have no setters, so every editable field is listed here.
 */
final class EntityFieldMap
{
    /** @var array<class-string, array<string, Closure(object, mixed): void>>|null */
    private static ?array $writers = null;

    /** @var array<class-string, array<string, Closure(object): mixed>>|null */
    private static ?array $readers = null;

    public static function writer(object $entity, string $field): ?Closure
    {
        self::$writers ??= self::writers();

        foreach (self::$writers as $class => $fields) {
            if ($entity instanceof $class) {
                return $fields[$field] ?? null;
            }
        }

        return null;
    }

    /**
     * Readers exist only for fields whose value object must reach the form as a plain string.
     */
    public static function reader(object $entity, string $field): ?Closure
    {
        self::$readers ??= self::readers();

        foreach (self::$readers as $class => $fields) {
            if ($entity instanceof $class) {
                return $fields[$field] ?? null;
            }
        }

        return null;
    }

    /**
     * @return array<class-string, array<string, Closure(object, mixed): void>>
     */
    private static function writers(): array
    {
        return [
            Vacancy::class => [
                'title'        => static fn (Vacancy $v, ?string $value) => $v->rename($value),
                'description'  => static fn (Vacancy $v, ?string $value) => $v->describe($value),
                'externalId'   => static fn (Vacancy $v, ?string $value) => $v->changeExternalId(ExternalId::fromNullable($value)),
                'url'          => static fn (Vacancy $v, ?string $value) => $v->changeUrl(Url::fromNullable($value)),
                'salary'       => static fn (Vacancy $v, ?string $value) => $v->changeSalary(Salary::fromNullable($value)),
                'viewed'       => static fn (Vacancy $v, mixed $value) => $v->changeViewed((bool) $value),
                'status'       => static fn (Vacancy $v, mixed $value) => $value ? $v->changeStatus($value) : null,
                'resumeSentAt' => static fn (Vacancy $v, mixed $value) => $v->markResumeSent(self::immutable($value)),
                'deadLineAt'   => static fn (Vacancy $v, mixed $value) => $v->scheduleDeadline(self::immutable($value)),
                'notes'        => static fn (Vacancy $v, iterable $notes) => self::sync($v->getNotes(), $notes, $v->attachNote(...), $v->detachNote(...)),
                'source'       => static fn (Vacancy $v, ?Source $value) => $v->moveToSource($value),
                'company'      => static fn (Vacancy $v, ?Company $value) => $v->assignCompany($value),
                'user'         => static fn (Vacancy $v, ?User $value) => $v->assignTo($value),
            ],
            Source::class => [
                'name'    => static fn (Source $s, ?string $value) => $s->rename($value),
                'url'     => static fn (Source $s, ?string $value) => $s->changeUrl(Url::fromNullable($value)),
                'active'  => static fn (Source $s, mixed $value) => $s->changeActive((bool) $value),
                'grabber' => static fn (Source $s, mixed $value) => $value ? $s->useGrabber($value) : null,
                'user'    => static fn (Source $s, ?User $value) => $s->assignTo($value),
            ],
            Company::class => [
                'name'  => static fn (Company $c, ?string $value) => $c->rename($value),
                'url'   => static fn (Company $c, ?string $value) => $c->changeUrl(Url::fromNullable($value)),
                'notes' => static fn (Company $c, iterable $notes) => self::sync($c->getNotes(), $notes, $c->attachNote(...), $c->detachNote(...)),
            ],
            Note::class => [
                'description' => static fn (Note $n, ?string $value) => $n->rewrite($value),
                'notedAt'     => static fn (Note $n, mixed $value) => $n->changeNotedAt(self::immutable($value)),
                'vacancy'     => static fn (Note $n, ?Vacancy $value) => $n->attachToVacancy($value),
                'company'     => static fn (Note $n, ?Company $value) => $n->attachToCompany($value),
                'user'        => static fn (Note $n, ?User $value) => $n->assignTo($value),
            ],
            GrabLog::class => [
                'description'           => static fn (GrabLog $l, ?string $value) => $l->annotate($value),
                'counterCompanyCreated' => static fn (GrabLog $l, mixed $value) => $l->correctCounters((int) $value, $l->getCounterVacancyCreated(), $l->getCounterVacancyUpdated()),
                'counterVacancyCreated' => static fn (GrabLog $l, mixed $value) => $l->correctCounters($l->getCounterCompanyCreated(), (int) $value, $l->getCounterVacancyUpdated()),
                'source'                => static fn (GrabLog $l, ?Source $value) => $l->attachToSource($value),
                'user'                  => static fn (GrabLog $l, ?User $value) => $l->assignTo($value),
            ],
            User::class => [
                'email'                      => static fn (User $u, ?string $value) => $u->changeEmail($value),
                'firstName'                  => static fn (User $u, ?string $value) => $u->rename($value, $u->getLastName()),
                'lastName'                   => static fn (User $u, ?string $value) => $u->rename($u->getFirstName(), $value),
                'active'                     => static fn (User $u, mixed $value) => $u->changeActive((bool) $value),
                'googleAuthenticatorEnabled' => static fn (User $u, mixed $value) => $u->changeGoogleAuthenticatorEnabled((bool) $value),
                'googleAuthenticatorToken'   => static fn (User $u, ?string $value) => $value ? $u->pairGoogleAuthenticator($value) : $u->resetGoogleAuthenticator(),
                'roles'                      => static fn (User $u, mixed $value) => $u->changeRoles((array) $value),
            ],
        ];
    }

    /**
     * Symfony's DateType hands over mutable dates; entities only take immutable ones.
     */
    private static function immutable(mixed $value): ?DateTimeImmutable
    {
        return $value instanceof DateTimeInterface ? DateTimeImmutable::createFromInterface($value) : null;
    }

    /**
     * Brings an entity collection in line with a submitted one through the entity's attach/detach methods.
     *
     * @param iterable<object> $current
     * @param iterable<object> $submitted
     */
    private static function sync(iterable $current, iterable $submitted, Closure $attach, Closure $detach): void
    {
        $submitted = [...$submitted];

        foreach ([...$current] as $item) {
            if (!in_array($item, $submitted, true)) {
                $detach($item);
            }
        }

        foreach ($submitted as $item) {
            $attach($item);
        }
    }

    /**
     * @return array<class-string, array<string, Closure(object): mixed>>
     */
    private static function readers(): array
    {
        return [
            Vacancy::class => [
                'externalId' => static fn (Vacancy $v) => $v->getExternalId()?->toString(),
                'url'        => static fn (Vacancy $v) => $v->getUrl()?->toString(),
                'salary'     => static fn (Vacancy $v) => $v->getSalary()?->toString(),
            ],
            Source::class => [
                'url' => static fn (Source $s) => $s->getUrl()?->toString(),
            ],
            Company::class => [
                'url' => static fn (Company $c) => $c->getUrl()?->toString(),
            ],
        ];
    }
}
