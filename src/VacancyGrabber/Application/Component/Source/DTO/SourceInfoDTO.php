<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Source\DTO;

use App\VacancyGrabber\Application\Component\Source\DTOFactory\SourceInfoDTOFactory;
use Carbon\CarbonImmutable;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;

/** @see SourceInfoDTOFactory */
final readonly class SourceInfoDTO
{
    public function __construct(
        #[Groups([
            'source_collection',
            'source_info',
        ])]
        public Uuid $id,

        #[Groups([
            'source_collection',
            'source_info',
        ])]
        public ?string $name,

        #[Groups([
            'source_collection',
            'source_info',
        ])]
        public ?string $url,

        #[Groups([
            'source_collection',
            'source_info',
        ])]
        public ?bool $active,

        #[Groups([
            'source_collection',
            'source_info',
        ])]
        #[OA\Property(type: 'string', format: 'date-time')]
        #[SerializedName('created_at')]
        public ?CarbonImmutable $createdAt,
    ) {
    }
}
