<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Note\DTOFactory;

use App\VacancyGrabber\Application\Note\DTO\NoteInfoDTO;
use App\VacancyGrabber\Domain\Note\Entity\Note;

final readonly class NoteInfoDTOFactory
{
    public function fromEntity(
        Note $entity,
    ): NoteInfoDTO {
        return new NoteInfoDTO(
            id: $entity->getId(),
            description: $entity->getDescription(),
            companyId: $entity->getVacancy()?->getCompany()?->getId(),
            companyName: $entity->getVacancy()?->getCompany()?->getName(),
            companyUrl: $entity->getVacancy()?->getCompany()?->getUrl()?->toString(),
            notedAt: $entity->getNotedAt(),
            createdAt: $entity->getCreatedAt(),
        );
    }
}
