<?php

declare(strict_types=1);

namespace App\Component\Vacancy\DTO;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

/** @see VacancyInfoDTOFactory */
final readonly class VacancyInfoDTO
{
    public function __construct(
        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        public Uuid $id,

        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        public ?string $name,

        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        public ?string $url,

        #[Groups([
            'vacancy_collection',
            'vacancy_info',
        ])]
        public ?bool $active,
    ) {
    }
}
