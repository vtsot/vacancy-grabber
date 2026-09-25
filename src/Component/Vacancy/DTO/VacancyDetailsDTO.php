<?php

declare(strict_types=1);

namespace App\Component\Vacancy\DTO;

use App\Component\Vacancy\Contract\Dictionary\VacancyStatus;
use App\Component\Vacancy\DTOFactory\VacancyDetailsDTOFactory;
use Carbon\CarbonImmutable;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;

/** @see VacancyDetailsDTOFactory */
final readonly class VacancyDetailsDTO
{
    public function __construct(
        #[Groups([
            'vacancy_details',
        ])]
        #[OA\Property(type: 'string', format: 'uuid')]
        public Uuid $id,

        #[Groups([
            'vacancy_details',
        ])]
        public ?string $title,

        #[Groups([
            'vacancy_details',
        ])]
        public ?string $description,

        #[Groups([
            'vacancy_details',
        ])]
        #[SerializedName('external_id')]
        public ?string $externalId,

        #[Groups([
            'vacancy_details',
        ])]
        public ?string $url,

        #[Groups([
            'vacancy_details',
        ])]
        public ?string $salary,

        #[Groups([
            'vacancy_details',
        ])]
        public ?string $source,

        #[Groups([
            'vacancy_details',
        ])]
        public ?string $company,

        #[Groups([
            'vacancy_details',
        ])]
        public ?VacancyStatus $status,

        #[Groups([
            'vacancy_details',
        ])]
        public array $notes,

        #[Groups([
            'vacancy_details',
        ])]
        public ?bool $viewed,

        #[Groups([
            'vacancy_details',
        ])]
        #[OA\Property(type: 'string', format: 'date-time')]
        #[SerializedName('resume_sent_at')]
        public ?CarbonImmutable $resumeSentAt,

        #[Groups([
            'vacancy_details',
        ])]
        #[OA\Property(type: 'string', format: 'date-time')]
        #[SerializedName('created_at')]
        public ?CarbonImmutable $createdAt,
    ) {
    }
}
