<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Shared\DTO;

use App\VacancyGrabber\Application\Shared\DTO\CollectionMeta\CollectionFiltersDTO;
use App\VacancyGrabber\Application\Shared\DTO\CollectionMeta\CollectionPaginatorDTO;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

final readonly class CollectionMetaDTO
{
    /** @param mixed[]|null $debug */
    public function __construct(
        #[Groups(['collection_meta'])]
        public CollectionPaginatorDTO $paginator,

        #[Groups(['collection_meta'])]
        #[OA\Property(
            ref: new Model(type: CollectionFiltersDTO::class, groups: ['collection_meta']),
        )]
        public ?CollectionFiltersDTO $filters,

        #[Groups(['collection_meta_debug'])]
        public ?array $debug = null,
    ) {
    }
}
