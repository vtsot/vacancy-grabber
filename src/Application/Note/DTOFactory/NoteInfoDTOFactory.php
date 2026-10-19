<?php

declare(strict_types=1);

namespace App\Application\Note\DTOFactory;

use App\Application\Note\DTO\NoteInfoDTO;
use App\Domain\Note\Entity\Note;

final readonly class NoteInfoDTOFactory
{
    public function fromEntity(
        Note $entity,
    ): NoteInfoDTO {
        return new NoteInfoDTO(
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
