<?php

declare(strict_types=1);

namespace App\Application\Company\DTOFactory;

use App\Application\Company\DTO\CompanyInfoDTO;
use App\Domain\Company\Entity\Company;

final readonly class CompanyInfoDTOFactory
{
    public function fromEntity(
        Company $entity,
    ): CompanyInfoDTO {
        return new CompanyInfoDTO(
            id: $entity->getId(),
            name: $entity->getName(),
            url: $entity->getUrl()?->toString(),
            createdAt: $entity->getCreatedAt(),
        );
    }
}
