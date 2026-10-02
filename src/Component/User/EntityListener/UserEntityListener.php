<?php

declare(strict_types=1);

namespace App\Component\User\EntityListener;

use App\Component\User\Contract\Entity\UserInterface;
use App\Contract\Entity\EntityListenerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

readonly class UserEntityListener implements EntityListenerInterface
{
    public function __construct(
        private PasswordHasherFactoryInterface $passwordHasherFactory,
    ) {
    }

    public function prePersist(UserInterface $user): void
    {
        $this->updateUserPassword($user);
    }

    public function preUpdate(UserInterface $user): void
    {
        $this->updateUserPassword($user);
    }

    private function updateUserPassword(UserInterface $user): void
    {
        $plainPassword = $user->getPlainPassword();

        if ($plainPassword !== null) {
            $user->setPassword($this->passwordHasherFactory->getPasswordHasher($user)->hash($plainPassword));
        }
    }
}
