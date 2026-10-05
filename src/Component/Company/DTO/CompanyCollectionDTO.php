<?php

declare(strict_types=1);

namespace App\Component\Company\DTO;

use App\Component\General\DTO\AbstractCollectionDTO;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

final readonly class CompanyCollectionDTO extends AbstractCollectionDTO
{
    /** @var CompanyInfoDTO[] */
    #[Groups(['company_collection'])]
    #[OA\Property(
        type: 'array',
        items: new OA\Items(
            ref: new Model(
                type: CompanyInfoDTO::class,
                groups: ['company_collection']
            ),
        )
    )]
    public array $items;
}
