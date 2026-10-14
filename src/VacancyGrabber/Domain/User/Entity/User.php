<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\User\Entity;

use App\VacancyGrabber\Domain\Shared\AbstractEntity;
use App\VacancyGrabber\Domain\User\PasswordHasher;

use function array_filter;
use function array_map;
use function array_unique;
use function array_values;
use function implode;
use function in_array;
use function trim;

class User extends AbstractEntity
{
    private ?string $email = null;

    private ?string $password = null;

    /** @var list<string> */
    private array $roles = [UserRole::User->value];

    private ?string $firstName = null;

    private ?string $lastName = null;

    private bool $active = true;

    private bool $googleAuthenticatorEnabled = false;

    private ?string $googleAuthenticatorToken = null;

    /**
     * @param list<UserRole|string> $roles
     */
    public static function register(
        string $email,
        ?string $firstName = null,
        ?string $lastName = null,
        array $roles = [UserRole::User],
    ): self {
        $user = new self();
        $user->changeEmail($email);
        $user->rename($firstName, $lastName);
        $user->changeRoles($roles);

        return $user;
    }

    public function __toString(): string
    {
        $labels = [
            $this->email ?? 'New Entity',
            $this->getFullName() ? '(' . $this->getFullName() . ')' : null,
        ];

        return implode(' ', array_filter($labels));
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function changeEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getFullName(): ?string
    {
        return trim(implode(' ', [$this->firstName, $this->lastName])) ?: null;
    }

    public function rename(?string $firstName, ?string $lastName): void
    {
        $this->firstName = $firstName;
        $this->lastName  = $lastName;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function changePassword(string $plainPassword, PasswordHasher $passwordHasher): void
    {
        $this->password = $passwordHasher->hash($plainPassword);
    }

    /**
     * Replaces the stored hash with one produced elsewhere (security layer rehash on login).
     */
    public function rehashPassword(string $hashedPassword): void
    {
        $this->password = $hashedPassword;
    }

    /**
     * @return list<string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }

    /**
     * @param list<UserRole|string> $roles
     */
    public function changeRoles(array $roles): void
    {
        $this->roles = array_values(array_unique(array_map(
            static fn (UserRole|string $role): string => $role instanceof UserRole ? $role->value : UserRole::from($role)->value,
            $roles,
        )));
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function deactivate(): void
    {
        $this->active = false;
    }

    public function changeActive(bool $active): void
    {
        $active ? $this->activate() : $this->deactivate();
    }

    public function isGoogleAuthenticatorEnabled(): bool
    {
        return $this->googleAuthenticatorEnabled;
    }

    public function getGoogleAuthenticatorToken(): ?string
    {
        return $this->googleAuthenticatorToken;
    }

    public function enableGoogleAuthenticator(): void
    {
        $this->googleAuthenticatorEnabled = true;
    }

    public function disableGoogleAuthenticator(): void
    {
        $this->googleAuthenticatorEnabled = false;
    }

    public function changeGoogleAuthenticatorEnabled(bool $enabled): void
    {
        $enabled ? $this->enableGoogleAuthenticator() : $this->disableGoogleAuthenticator();
    }

    public function pairGoogleAuthenticator(string $secret): void
    {
        $this->googleAuthenticatorToken = $secret;
    }

    public function resetGoogleAuthenticator(): void
    {
        $this->googleAuthenticatorToken = null;
    }
}
