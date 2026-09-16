<?php

declare(strict_types=1);

namespace App\Component\Source\DTO;

use App\Component\Source\DTOFactory\SourceDetailsDTOFactory;
use Symfony\Component\Serializer\Attribute\Groups;
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
    ) {
    }
}
