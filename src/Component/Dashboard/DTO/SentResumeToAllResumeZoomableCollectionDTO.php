<?php

declare(strict_types=1);

namespace App\Component\Dashboard\DTO;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

final readonly class SentResumeToAllResumeZoomableCollectionDTO
{
    public function __construct(
        /** @var SentResumeToAllResumeZoomableInfoDTO[] */
        #[Groups(['dashboard'])]
        #[OA\Property(
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: SentResumeToAllResumeZoomableInfoDTO::class,
                    groups: ['dashboard']
                ),
            )
        )]
        public array $items,
    ) {
    }
}
