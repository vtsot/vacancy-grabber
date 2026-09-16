<?php

declare(strict_types=1);

namespace App\Component\Source\DTO;

use Symfony\Component\Serializer\Attribute\Groups;
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
    ) {
    }
}
