<?php

declare(strict_types=1);

namespace App\Application\Vacancy\Message\Command;

use App\Application\Vacancy\MessageHandler\Command\PatchVacancyHandler;
use App\Domain\Vacancy\Entity\VacancyStatus;
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
        public ?CarbonImmutable $deadLineAt = null,
        public ?VacancyStatus $status = null,
    ) {
    }
}
