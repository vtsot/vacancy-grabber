<?php

declare(strict_types=1);

namespace App\Component\User\Entity;

use App\Component\General\Shared\AbstractEntity;
use App\Component\General\Shared\EntityTrait\ActiveEntityTrait;
use App\Component\User\Contract\Dictionary\UserRole;
use App\Component\User\Contract\Entity\UserInterface;
use App\Component\User\EntityListener\UserEntityListener;
use App\Component\User\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use function array_filter;
use function implode;
use function in_array;

#[ORM\Table(name: 'users')]
#[ORM\Index(name: 'idx_active', columns: ['active'])]
#[ORM\UniqueConstraint(name: 'uniq_email', columns: ['email'])]
#[ORM\EntityListeners([UserEntityListener::class])]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User extends AbstractEntity implements UserInterface
{
    use ActiveEntityTrait;

    #[ORM\Column(name: 'email', type: Types::STRING, length: 255, unique: true, nullable: false)]
    private ?string $email = null;

    private ?string $plainPassword = null;

    #[ORM\Column(name: 'password', type: Types::STRING, length: 255, nullable: true)]
    private ?string $password = null;

    #[ORM\Column(name: 'roles', type: Types::JSON, nullable: false)]
    private array $roles = [UserRole::User->value];

    public function __toString(): string
    {
        $labels   = [];
        $labels[] = $this->getEmail() ?? 'New Entity';

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

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

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
