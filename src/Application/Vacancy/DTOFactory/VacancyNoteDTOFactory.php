<?php

declare(strict_types=1);

namespace App\Application\Vacancy\DTOFactory;

use App\Application\Vacancy\DTO\VacancyNoteDTO;
use App\Domain\Note\Entity\Note;

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
