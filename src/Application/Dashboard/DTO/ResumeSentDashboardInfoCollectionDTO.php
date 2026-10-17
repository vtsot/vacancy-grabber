<?php

declare(strict_types=1);

namespace App\Application\Dashboard\DTO;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

final readonly class ResumeSentDashboardInfoCollectionDTO
{
    public function __construct(
        /** @var ResumeSentDashboardInfoDTO[] */
        #[Groups(['dashboard'])]
        #[OA\Property(
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: ResumeSentDashboardInfoDTO::class,
                    groups: ['dashboard']
                ),
            )
        )]
        public array $items,
    ) {
    }
}
