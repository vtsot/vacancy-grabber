<?php

declare(strict_types=1);

namespace App\Component\General\DTO;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

abstract readonly class AbstractCollectionDTO
{
    public function __construct(
        /** @var mixed[] */
        public array $items,

        #[Groups(['collection_meta'])]
        #[OA\Property(
            ref: new Model(type: CollectionMetaDTO::class, groups: ['collection_meta']),
        )]
        public ?CollectionMetaDTO $meta,
    ) {
    }
}
