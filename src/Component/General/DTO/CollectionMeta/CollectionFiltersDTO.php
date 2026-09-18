<?php

declare(strict_types=1);

namespace App\Component\General\DTO\CollectionMeta;

use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class CollectionFiltersDTO
{
    public function __construct(
        #[Groups(['collection_meta'])]
        public ?string $query = null,

        #[Groups('collection_meta')]
        public ?string $sort = null,

        #[Groups('collection_meta')]
        #[OA\Property(type: 'array', items: new OA\Items(type: 'string'))]
        #[SerializedName('sort_by')]
        public ?array $sortBy = [],

        #[Groups(['collection_meta'])]
        #[OA\Property(type: 'array', items: new OA\Items(type: 'string'))]
        public ?array $available = [],

        #[Groups(['collection_meta'])]
        #[OA\Property(type: 'array', items: new OA\Items(type: 'string'))]
        public ?array $applied = [],
    ) {
    }
}
