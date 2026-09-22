<?php

declare(strict_types=1);

namespace App\Component\Company\DTO;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

/** @see CompanySummaryDTOFactory */
final readonly class CompanySummaryDTO
{
    public function __construct(
        #[Groups([
            'company_summary',
        ])]
        public Uuid $id,

        #[Groups([
            'company_summary',
        ])]
        public ?string $name,
    ) {
    }
}
