<?php

declare(strict_types=1);

namespace App\Component\User\Entity\Traits;

trait PlainPasswordEntityTrait
{
    private ?string $plainPassword = null;

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }
}
