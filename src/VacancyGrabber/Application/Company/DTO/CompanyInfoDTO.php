<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Company\DTO;

use App\VacancyGrabber\Application\Company\DTOFactory\CompanyInfoDTOFactory;
use Carbon\CarbonImmutable;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;

/** @see CompanyInfoDTOFactory */
final readonly class CompanyInfoDTO
{
    public function __construct(
        #[Groups([
            'company_collection',
            'company_info',
        ])]
        public Uuid $id,

        #[Groups([
            'company_collection',
            'company_info',
        ])]
        public ?string $name,

        #[Groups([
            'company_collection',
            'company_info',
        ])]
        public ?string $url,

        #[Groups([
            'company_collection',
            'company_info',
        ])]
        #[OA\Property(type: 'string', format: 'date-time')]
        #[SerializedName('created_at')]
        public ?CarbonImmutable $createdAt,
    ) {
    }
}
