<?php

declare(strict_types=1);

namespace App\Component\GrabLog\DTO;

use Carbon\CarbonImmutable;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
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
        public ?string $source,

        #[Groups([
            'grab_log_collection',
            'grab_log_info',
        ])]
        public ?string $description,

        #[Groups([
            'grab_log_collection',
            'grab_log_info',
        ])]
        #[SerializedName('counter_company_created')]
        public ?int $counterCompanyCreated = 0,

        #[Groups([
            'grab_log_collection',
            'grab_log_info',
        ])]
        #[SerializedName('counter_vacancy_created')]
        public ?int $counterVacancyCreated = 0,

        #[Groups([
            'grab_log_collection',
            'grab_log_info',
        ])]
        #[OA\Property(type: 'string', format: 'date-time')]
        #[SerializedName('created_at')]
        public readonly ?CarbonImmutable $createdAt,
    ) {
    }
}
