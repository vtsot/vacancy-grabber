<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\DTO;

use App\VacancyGrabber\Domain\Vacancy\Entity\VacancyStatus;
use Carbon\CarbonImmutable;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;

/** @see VacancyInfoDTOFactory */
final readonly class VacancyInfoDTO
{
    public function __construct(
        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        #[OA\Property(type: 'string', format: 'uuid')]
        public Uuid $id,

        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        public ?string $title,

        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        public ?string $description,

        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        #[SerializedName('external_id')]
        public ?string $externalId,

        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        public ?string $url,

        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        public ?string $salary,

        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        #[SerializedName('source_id')]
        public ?Uuid $sourceId,

        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        #[SerializedName('source_name')]
        public ?string $sourceName,

        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        #[SerializedName('company_id')]
        public ?Uuid $companyId,

        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        #[SerializedName('company_name')]
        public ?string $companyName,

        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        public ?VacancyStatus $status,

        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        #[SerializedName('notes_count')]
        public ?int $notesCount,

        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        public ?bool $viewed,

        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        #[SerializedName('is_resume_sent')]
        public ?bool $isResumeSent,

        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        #[OA\Property(type: 'string', format: 'date-time')]
        #[SerializedName('resume_sent_at')]
        public ?CarbonImmutable $resumeSentAt,

        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        #[OA\Property(type: 'string', format: 'date-time')]
        #[SerializedName('dead_line_at')]
        public ?CarbonImmutable $deadLineAt,

        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        #[OA\Property(type: 'string', format: 'date-time')]
        #[SerializedName('created_at')]
        public ?CarbonImmutable $createdAt,
    ) {
    }
}
