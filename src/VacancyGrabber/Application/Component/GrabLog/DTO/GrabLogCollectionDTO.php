<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\GrabLog\DTO;

use App\VacancyGrabber\Application\DTO\AbstractCollectionDTO;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

final readonly class GrabLogCollectionDTO extends AbstractCollectionDTO
{
    /** @var GrabLogInfoDTO[] */
    #[Groups(['grab_log_collection'])]
    #[OA\Property(
        type: 'array',
        items: new OA\Items(
            ref: new Model(
                type: GrabLogInfoDTO::class,
                groups: ['grab_log_collection']
            ),
        )
    )]
    public array $items;
}
