<?php

declare(strict_types=1);

namespace App\Component\Note\DTOFactory;

use App\Component\Note\DTO\NoteInfoDTO;
use App\Component\Note\Entity\Note;

final readonly class NoteInfoDTOFactory
{
    public function fromEntity(
        Note $entity,
    ): NoteInfoDTO {
        return new NoteInfoDTO(
            id: $entity->getId(),
            description: $entity->getDescription(),
            companyId: $entity->getVacancy()->getCompany()?->getId(),
            companyName: $entity->getVacancy()->getCompany()?->getName(),
            companyUrl: $entity->getVacancy()->getCompany()?->getUrl(),
            notedAt: $entity->getNotedAt(),
            createdAt: $entity->getCreatedAt(),
        );
    }
}
