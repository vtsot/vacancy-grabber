<?php

declare(strict_types=1);

namespace App\Component\Vacancy\DTO;

use App\Component\Vacancy\DTOFactory\VacancyNoteDTOFactory;
use Carbon\CarbonImmutable;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;

/** @see VacancyNoteDTOFactory */
final readonly class VacancyNoteDTO
{
    public function __construct(
        #[Groups([
            'vacancy_note_collection',
            'vacancy_note_info',
            'vacancy_note_details',
        ])]
        #[OA\Property(type: 'string', format: 'uuid')]
        public Uuid $id,

        #[Groups([
            'vacancy_note_collection',
            'vacancy_note_info',
            'vacancy_note_details',
        ])]
        public ?string $description,

        #[Groups([
            'vacancy_note_collection',
            'vacancy_note_info',
            'vacancy_note_details',
        ])]
        #[OA\Property(type: 'string', format: 'date-time')]
        #[SerializedName('noted_at')]
        public ?CarbonImmutable $notedAt,
    ) {
    }
}
