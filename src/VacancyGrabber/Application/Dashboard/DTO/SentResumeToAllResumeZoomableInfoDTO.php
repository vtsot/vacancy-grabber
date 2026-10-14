<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Dashboard\DTO;

use Carbon\CarbonImmutable;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class SentResumeToAllResumeZoomableInfoDTO
{
    public function __construct(
        #[Groups([
            'dashboard',
        ])]
        #[OA\Property(type: 'string', format: 'date')]
        #[SerializedName('date')]
        public ?CarbonImmutable $date,

        #[Groups([
            'dashboard',
        ])]
        public ?int $vacancies,

        #[Groups([
            'dashboard',
        ])]
        #[SerializedName('resume_sent')]
        public ?int $resumeSent,

        #[Groups([
            'dashboard',
        ])]
        #[SerializedName('dead_line')]
        public ?int $deadLine,
    ) {
    }
}
