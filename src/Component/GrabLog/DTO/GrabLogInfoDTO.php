<?php

declare(strict_types=1);

namespace App\Component\GrabLog\DTO;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

/** @see GrabLogInfoDTOFactory */
final readonly class GrabLogInfoDTO
{
    public function __construct(
        #[Groups([
            'grab_log_collection',
            'grab_log_info',
        ])]
        public Uuid $id,

        #[Groups([
            'grab_log_collection',
            'grab_log_info',
        ])]
        public ?string $description,

        #[Groups([
            'grab_log_collection',
            'grab_log_info',
        ])]
        public ?int $counterCompanyCreated = 0,

        #[Groups([
            'grab_log_collection',
            'grab_log_info',
        ])]
        public ?int $counterVacancyCreated = 0,
    ) {
    }
}
