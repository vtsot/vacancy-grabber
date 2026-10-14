<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Note\DTO;

use App\VacancyGrabber\Application\DTO\AbstractCollectionDTO;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

final readonly class NoteCollectionDTO extends AbstractCollectionDTO
{
    /** @var NoteInfoDTO[] */
    #[Groups(['note_collection'])]
    #[OA\Property(
        type: 'array',
        items: new OA\Items(
            ref: new Model(
                type: NoteInfoDTO::class,
                groups: ['note_collection']
            ),
        )
    )]
    public array $items;
}
