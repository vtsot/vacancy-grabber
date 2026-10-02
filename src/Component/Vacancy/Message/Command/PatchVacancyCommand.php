<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Message\Command;

use App\Component\Vacancy\Contract\Dictionary\VacancyStatus;
use App\Component\Vacancy\MessageHandler\Command\PatchVacancyHandler;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

/** @see PatchVacancyHandler */
final readonly class PatchVacancyCommand
{
    public function __construct(
        public ?Uuid $userId,
        public Uuid $vacancyId,
        public array $properties,
        public ?string $title = null,
        public ?string $description = null,
        public ?string $externalId = null,
        public ?string $url = null,
        public ?string $salary = null,
        public ?bool $viewed = null,
        public ?CarbonImmutable $resumeSentAt = null,
        public ?VacancyStatus $status = null,
    ) {
    }
}
