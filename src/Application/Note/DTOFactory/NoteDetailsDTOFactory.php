<?php

declare(strict_types=1);

namespace App\Application\Note\DTOFactory;

use App\Application\Note\DTO\NoteDetailsDTO;
use App\Domain\Note\Entity\Note;

final readonly class NoteDetailsDTOFactory
{
    public function fromEntity(
        Note $entity,
    ): NoteDetailsDTO {
        return new NoteDetailsDTO(
            id: $entity->getId(),
            description: $entity->getDescription(),
            companyId: $entity->getCompany()?->getId(),
            companyName: $entity->getCompany()?->getName(),
            companyUrl: $entity->getCompany()?->getUrl()?->toString(),
            vacancyId: $entity->getVacancy()?->getId(),
            notedAt: $entity->getNotedAt(),
            createdAt: $entity->getCreatedAt(),
        );
    }
}
