<?php

declare(strict_types=1);

namespace App\Tests\Suite\Unit\Application;

use App\VacancyGrabber\Application\Vacancy\Service\VacancyFieldWriter;
use App\VacancyGrabber\Domain\Vacancy\Entity\Vacancy;
use App\VacancyGrabber\Domain\Vacancy\Entity\VacancyStatus;
use Codeception\Test\Unit;
use DateTimeImmutable;

final class VacancyFieldWriterTest extends Unit
{
    public function testResumeDateWinsOverViewedFalseInTheSamePatch(): void
    {
        $vacancy = Vacancy::create(owner: null, title: 'A');

        // property order in the request must not matter
        new VacancyFieldWriter()->apply($vacancy, [
            'resumeSentAt' => new DateTimeImmutable('2026-10-01'),
            'viewed'       => false,
        ]);

        self::assertTrue($vacancy->isViewed());
    }

    public function testAppliesKnownFieldsAndIgnoresOthers(): void
    {
        $vacancy = Vacancy::create(owner: null, title: 'A');

        new VacancyFieldWriter()->apply($vacancy, [
            'title'   => 'B',
            'url'     => 'https://example.com/b',
            'status'  => VacancyStatus::Done->value,
            'unknown' => 'ignored',
        ]);

        self::assertSame('B', $vacancy->getTitle());
        self::assertSame('https://example.com/b', $vacancy->getUrl()?->toString());
        self::assertSame(VacancyStatus::Done, $vacancy->getStatus());
    }
}
