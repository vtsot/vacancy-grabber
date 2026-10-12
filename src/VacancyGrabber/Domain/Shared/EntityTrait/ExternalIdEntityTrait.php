<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Shared\EntityTrait;

// use Doctrine\DBAL\Types\Types;
// use Doctrine\ORM\Mapping as ORM;

trait ExternalIdEntityTrait
{
    //    #[ORM\Column(name: 'external_id', type: Types::STRING, length: 255, nullable: true)]
    private ?string $externalId = null;

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): static
    {
        $this->externalId = $externalId;

        return $this;
    }
}
