<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\DTO;

use App\VacancyGrabber\Application\Vacancy\DTOFactory\VacancyCriteriaDTOFactory;
use App\VacancyGrabber\Domain\Vacancy\Entity\VacancyStatus;
use Carbon\CarbonImmutable;
use Symfony\Component\Uid\Uuid;

/** @see VacancyCriteriaDTOFactory */
final readonly class VacancyCriteriaDTO
{
    public function __construct(
        public ?Uuid $userId = null,
        public ?int $page = null,
        public ?int $perPage = null,
        public ?string $query = null,
        public ?string $orderBy = null,
        public ?string $orderDirection = null,
        public ?string $externalId = null,
        public ?bool $viewed = null,
        public ?CarbonImmutable $startDay = null,
        public ?CarbonImmutable $endDay = null,
        public ?Uuid $source = null,
        public ?Uuid $company = null,
        public ?VacancyStatus $status = null,
        public ?bool $isResumeSent = null,
        public ?CarbonImmutable $resumeSentAt = null,
        public ?CarbonImmutable $resumeSentAtStartDay = null,
        public ?CarbonImmutable $resumeSentAtEndDay = null,
        public ?bool $isDeadLine = null,
        public ?CarbonImmutable $deadLineAt = null,
        public ?CarbonImmutable $deadLineAtStartDay = null,
        public ?CarbonImmutable $deadLineAtEndDay = null,
    ) {
    }
}
