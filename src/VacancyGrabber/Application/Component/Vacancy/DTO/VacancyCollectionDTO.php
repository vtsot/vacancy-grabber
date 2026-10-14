<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Vacancy\DTO;

use App\VacancyGrabber\Application\Shared\DTO\AbstractCollectionDTO;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

final readonly class VacancyCollectionDTO extends AbstractCollectionDTO
{
    /** @var VacancyInfoDTO[] */
    #[Groups(['vacancy_collection'])]
    #[OA\Property(
        type: 'array',
        items: new OA\Items(
            ref: new Model(
                type: VacancyInfoDTO::class,
                groups: ['vacancy_collection']
            ),
        )
    )]
    public array $items;
}
