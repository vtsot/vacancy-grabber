<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Source\DTO;

use App\VacancyGrabber\Application\Source\DTOFactory\SourceDetailsDTOFactory;
use Carbon\CarbonImmutable;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;

/** @see SourceDetailsDTOFactory */
final readonly class SourceDetailsDTO
{
    public function __construct(
        #[Groups([
            'source_details',
        ])]
        public Uuid $id,

        #[Groups([
            'source_details',
        ])]
        public ?string $name,

        #[Groups([
            'source_details',
        ])]
        public ?string $url,

        #[Groups([
            'source_details',
        ])]
        public ?bool $active,

        #[Groups([
            'source_details',
        ])]
        #[OA\Property(type: 'string', format: 'date-time')]
        #[SerializedName('created_at')]
        public ?CarbonImmutable $createdAt,
    ) {
    }
}
