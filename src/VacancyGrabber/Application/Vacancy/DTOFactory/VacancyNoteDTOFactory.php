<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\DTOFactory;

use App\VacancyGrabber\Application\Vacancy\DTO\VacancyNoteDTO;
use App\VacancyGrabber\Domain\Note\Entity\Note;

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
