<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Infrastructure\Security;

use App\VacancyGrabber\Domain\User\Entity\User;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Symfony Security view of the domain User; keeps framework interfaces out of the Domain.
 */
final readonly class SecurityUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        private User $user,
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getId(): ?Uuid
    {
        return $this->user->getId();
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->user->getEmail();
    }

    public function getRoles(): array
    {
        return $this->user->getRoles();
    }

    public function hasRole(string $role): bool
    {
        return $this->user->hasRole($role);
    }

    public function getPassword(): ?string
    {
        return $this->user->getPassword();
    }

    public function eraseCredentials(): void
    {
    }

    public function __toString(): string
    {
        return (string) $this->user;
    }
}
