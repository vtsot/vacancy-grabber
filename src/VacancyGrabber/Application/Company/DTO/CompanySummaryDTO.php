<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Company\DTO;

use App\VacancyGrabber\Application\Company\DTOFactory\CompanySummaryDTOFactory;
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
