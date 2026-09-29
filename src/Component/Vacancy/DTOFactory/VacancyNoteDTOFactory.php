<?php

declare(strict_types=1);

namespace App\Component\Vacancy\DTOFactory;

use App\Component\Note\Entity\Note;
use App\Component\Vacancy\DTO\VacancyNoteDTO;

final readonly class VacancyNoteDTOFactory
{
    public function fromEntity(
        Note $entity,
    ): VacancyNoteDTO {
        return new VacancyNoteDTO(
            id: $entity->getId(),
            description: $entity->getDescription(),
            notedAt: $entity->getNotedAt(),
        );
    }
}
