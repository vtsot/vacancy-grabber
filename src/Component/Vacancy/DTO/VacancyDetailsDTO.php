<?php

declare(strict_types=1);

namespace App\Component\Vacancy\DTO;

use App\Component\Vacancy\DTOFactory\VacancyDetailsDTOFactory;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

/** @see VacancyDetailsDTOFactory */
final readonly class VacancyDetailsDTO
{
    public function __construct(
        #[Groups([
            'vacancy_details',
        ])]
        public Uuid $id,

        #[Groups([
            'vacancy_details',
        ])]
        public ?string $name,

        #[Groups([
            'vacancy_details',
        ])]
        public ?string $url,

        #[Groups([
            'vacancy_details',
        ])]
        public ?bool $active,
    ) {
    }
}
