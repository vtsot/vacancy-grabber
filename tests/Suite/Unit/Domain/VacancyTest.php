<?php

declare(strict_types=1);

namespace App\Tests\Suite\Unit\Domain;

use App\Domain\Shared\Exception\InvalidArgumentException;
use App\Domain\Shared\ValueObject\Url;
use App\Domain\Source\Entity\Source;
use App\Domain\Source\Grabber\GrabbedVacancy;
use App\Domain\Vacancy\Entity\Vacancy;
use App\Domain\Vacancy\ValueObject\Salary;
use Codeception\Test\Unit;
use DateTimeImmutable;

final class VacancyTest extends Unit
{
    public function testNewResumeDateMarksVacancyViewed(): void
    {
        $vacancy = Vacancy::create(owner: null, title: 'PHP developer');

        $vacancy->markResumeSent(new DateTimeImmutable('2026-10-01'));

        self::assertTrue($vacancy->isViewed());
    }

    public function testSameResumeDateKeepsExplicitViewedFlag(): void
    {
        $vacancy = Vacancy::create(owner: null, title: 'PHP developer');
        $vacancy->markResumeSent(new DateTimeImmutable('2026-10-01'));
        $vacancy->markUnviewed();

        // e.g. an admin form re-submitting the unchanged date
        $vacancy->markResumeSent(new DateTimeImmutable('2026-10-01'));

        self::assertFalse($vacancy->isViewed());
    }

    public function testDeadlineAndNotesMarkVacancyViewed(): void
    {
        $withDeadline = Vacancy::create(owner: null, title: 'A');
        $withDeadline->scheduleDeadline(new DateTimeImmutable('2026-11-01'));

        $withNote = Vacancy::create(owner: null, title: 'B');
        $note     = $withNote->writeNote(null, 'Called the recruiter', new DateTimeImmutable('2026-10-01'));

        self::assertTrue($withDeadline->isViewed());
        self::assertTrue($withNote->isViewed());
        self::assertSame($withNote, $note->getVacancy());
        self::assertTrue($withNote->getNotes()->contains($note));
    }

    public function testDetachNoteUnlinksBothSides(): void
    {
        $vacancy = Vacancy::create(owner: null, title: 'A');
        $note    = $vacancy->writeNote(null, 'text', new DateTimeImmutable());

        $vacancy->detachNote($note);

        self::assertNull($note->getVacancy());
        self::assertCount(0, $vacancy->getNotes());
    }

    public function testGrabbedVacancyDropsMalformedValues(): void
    {
        $vacancy = Vacancy::grabbed(null, new Source(), null, new GrabbedVacancy(
            externalId: ' 42 ',
            url: 'not a url',
            title: 'Go developer',
            description: null,
            salary: "  $3000 \n - 4000 ",
            company: null,
        ));

        self::assertSame('42', $vacancy->getExternalId()?->toString());
        self::assertNull($vacancy->getUrl());
        self::assertSame('$3000 - 4000', $vacancy->getSalary()?->toString());
    }

    public function testValueObjectsRejectInvalidInput(): void
    {
        self::assertNull(Url::fromNullable('  '));
        self::assertNull(Salary::fromNullable(null));

        $this->expectException(InvalidArgumentException::class);
        Url::fromString('ftp//broken');
    }
}
