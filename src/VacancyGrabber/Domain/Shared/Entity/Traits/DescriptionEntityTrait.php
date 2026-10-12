<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Shared\Entity\Traits;

// use Doctrine\DBAL\Types\Types;
// use Doctrine\ORM\Mapping as ORM;

trait DescriptionEntityTrait
{
    //    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    protected ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
