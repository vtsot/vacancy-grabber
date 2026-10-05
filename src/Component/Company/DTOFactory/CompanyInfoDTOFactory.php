<?php

declare(strict_types=1);

namespace App\Component\Company\DTOFactory;

use App\Component\Company\DTO\CompanyInfoDTO;
use App\Component\Company\Entity\Company;

final readonly class CompanyInfoDTOFactory
{
    public function fromEntity(
        Company $entity,
    ): CompanyInfoDTO {
        return new CompanyInfoDTO(
            id: $entity->getId(),
            name: $entity->getName(),
            url: $entity->getUrl(),
            createdAt: $entity->getCreatedAt(),
        );
    }
}
