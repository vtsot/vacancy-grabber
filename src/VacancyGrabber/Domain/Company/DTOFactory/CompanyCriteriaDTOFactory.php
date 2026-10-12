<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Company\DTOFactory;

class CompanyCriteriaDTOFactory
{
    public function fromRequest(
        $request,
    ): CompanyCriteriaDTO {
        return new CompanyCriteriaDTO(
            //            id: $entity->getId(),
            //            name: $entity->getName(),
            //            url: $entity->getUrl(),
            //            createdAt: $entity->getCreatedAt(),
        );
    }
}
