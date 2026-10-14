<?php

declare(strict_types=1);

namespace App\Application\Source\DTO;

use App\Application\Source\DTOFactory\SourceSummaryDTOFactory;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

/** @see SourceSummaryDTOFactory */
final readonly class SourceSummaryDTO
{
    public function __construct(
        #[Groups([
            'source_summary',
        ])]
        public Uuid $id,

        #[Groups([
            'source_summary',
        ])]
        public ?string $name,
    ) {
    }
}
