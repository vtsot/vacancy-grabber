<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Message\Command;

use App\Component\Vacancy\Contract\Dictionary\VacancyStatus;
use App\Component\Vacancy\MessageHandler\Command\CreateVacancyHandler;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

/** @see CreateVacancyHandler */
final readonly class CreateVacancyCommand
{
    public function __construct(
        public ?Uuid $userId = null,
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
