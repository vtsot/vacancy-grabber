<?php

declare(strict_types=1);

namespace App\Component\Note\DTOFactory;

use App\Component\Note\DTO\NoteDetailsDTO;
use App\Component\Note\Entity\Note;

final readonly class NoteDetailsDTOFactory
{
    public function fromEntity(
        Note $entity,
    ): NoteDetailsDTO {
        return new NoteDetailsDTO(
            id: $entity->getId(),
            description: $entity->getDescription(),
            company: $entity->getCompany()?->getName(),
            notedAt: $entity->getNotedAt(),
            createdAt: $entity->getCreatedAt(),
        );
    }
}
