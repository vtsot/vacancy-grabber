<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Source\DTO;

use App\Component\General\DTO\AbstractCollectionDTO;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

final readonly class SourceCollectionDTO extends AbstractCollectionDTO
{
    /** @var SourceInfoDTO[] */
    #[Groups(['source_collection'])]
    #[OA\Property(
        type: 'array',
        items: new OA\Items(
            ref: new Model(
                type: SourceInfoDTO::class,
                groups: ['source_collection']
            ),
        )
    )]
    public array $items;
}
