<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Shared\EntityTrait;

use function implode;
use function trim;

trait UserNameEntityTrait
{
    private ?string $firstName = null;

    private ?string $lastName = null;

    public function getFullName(): ?string
    {
        return trim(implode(' ', [$this->firstName, $this->lastName])) ?: null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }
}
