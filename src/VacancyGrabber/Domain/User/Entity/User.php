<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\User\Entity;

use App\Contract\Entity\UserInterface;
use App\VacancyGrabber\Domain\Shared\AbstractEntity;
use App\VacancyGrabber\Domain\Shared\EntityTrait\ActiveEntityTrait;
use App\VacancyGrabber\Domain\Shared\EntityTrait\GoogleAuthenticatorEntityTrait;
use App\VacancyGrabber\Domain\Shared\EntityTrait\PlainPasswordEntityTrait;
use App\VacancyGrabber\Domain\Shared\EntityTrait\UserNameEntityTrait;

use function array_filter;
use function implode;
use function in_array;

class User extends AbstractEntity implements UserInterface
{
    use ActiveEntityTrait;
    use PlainPasswordEntityTrait;
    use GoogleAuthenticatorEntityTrait;
    use UserNameEntityTrait;

    private ?string $email = null;

    private ?string $password = null;

    private array $roles = [UserRole::User->value];

    public function __toString(): string
    {
        $labels   = [];
        $labels[] = $this->getEmail() ?? 'New Entity';
        $labels[] = $this->getFullName() ? '(' . $this->getFullName() . ')' : null;

        return implode(' ', array_filter($labels));
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles(), true);
    }
}
