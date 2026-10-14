<?php

declare(strict_types=1);

namespace App\Application\Note\DTO;

use App\Application\Shared\DTO\AbstractCollectionDTO;
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
