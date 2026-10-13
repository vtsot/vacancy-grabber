<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\Message\Command;

use App\VacancyGrabber\Application\Vacancy\MessageHandler\Command\UpdateVacancyHandler;
use App\VacancyGrabber\Domain\Vacancy\Entity\VacancyStatus;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

/** @see UpdateVacancyHandler */
final readonly class UpdateVacancyCommand
{
    public function __construct(
        public ?Uuid $userId,
        public Uuid $vacancyId,
        public ?string $title = null,
        public ?string $description = null,
        public ?string $externalId = null,
        public ?string $url = null,
        public ?string $salary = null,
        public ?Uuid $sourceId = null,
        public ?Uuid $companyId = null,
        public ?VacancyStatus $status = null,
        public ?bool $viewed = null,
        public ?CarbonImmutable $resumeSentAt = null,
        public ?CarbonImmutable $deadLineAt = null,
    ) {
    }
}
