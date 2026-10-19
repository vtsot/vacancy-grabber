<?php

declare(strict_types=1);

namespace App\Application\Note\DTO;

use App\Application\Note\DTOFactory\NoteDetailsDTOFactory;
use Carbon\CarbonImmutable;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;

/** @see NoteDetailsDTOFactory */
readonly class NoteDetailsDTO
{
    public function __construct(
        #[Groups([
            'note_collection',
            'note_info',
            'note_details',
        ])]
        #[OA\Property(type: 'string', format: 'uuid')]
        public Uuid $id,

        #[Groups([
            'note_collection',
            'note_info',
            'note_details',
        ])]
        public ?string $description,

        #[Groups([
            'note_collection',
            'note_info',
            'note_details',
        ])]
        #[OA\Property(type: 'string', format: 'uuid')]
        #[SerializedName('company_id')]
        public ?Uuid $companyId,

        #[Groups([
            'note_collection',
            'note_info',
            'note_details',
        ])]
        #[SerializedName('company_name')]
        public ?string $companyName,

        #[Groups([
            'note_collection',
            'note_info',
            'note_details',
        ])]
        #[SerializedName('company_url')]
        public ?string $companyUrl,

        #[Groups([
            'note_collection',
            'note_info',
            'note_details',
        ])]
        #[SerializedName('vacancy_id')]
        public ?Uuid $vacancyId,

        #[Groups([
            'note_collection',
            'note_info',
            'note_details',
        ])]
        #[OA\Property(type: 'string', format: 'date-time')]
        #[SerializedName('noted_at')]
        public ?CarbonImmutable $notedAt,

        #[Groups([
            'note_collection',
            'note_info',
            'note_details',
        ])]
        #[OA\Property(type: 'string', format: 'date-time')]
        #[SerializedName('created_at')]
        public ?CarbonImmutable $createdAt,
    ) {
    }
}
