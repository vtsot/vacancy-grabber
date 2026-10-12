<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Shared\EntityTrait;

// use Carbon\CarbonImmutable;
// use Doctrine\DBAL\Types\Types;
// use Doctrine\ORM\Mapping as ORM;
// use Gedmo\Mapping\Annotation as Gedmo;

use Carbon\CarbonImmutable;

trait TimeStampableEntityTrait
{
    //    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE, precision: 6, nullable: true)]
    //    #[Gedmo\Timestampable(on: 'create')]
    protected ?CarbonImmutable $createdAt;

    //    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_IMMUTABLE, precision: 6, nullable: true)]
    //    #[Gedmo\Timestampable(on: 'update')]
    protected ?CarbonImmutable $updatedAt;

    public function setCreatedAt(CarbonImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): ?CarbonImmutable
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(CarbonImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?CarbonImmutable
    {
        return $this->updatedAt;
    }
}
