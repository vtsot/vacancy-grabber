<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\DTO;

use App\VacancyGrabber\Application\Shared\DTO\AbstractCollectionDTO;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

final readonly class VacancyNoteCollectionDTO extends AbstractCollectionDTO
{
    /** @var VacancyNoteDTO[] */
    #[Groups(['vacancy_note_collection'])]
    #[OA\Property(
        type: 'array',
        items: new OA\Items(
            ref: new Model(
                type: VacancyNoteDTO::class,
                groups: ['vacancy_note_collection']
            ),
        )
    )]
    public array $items;
}
