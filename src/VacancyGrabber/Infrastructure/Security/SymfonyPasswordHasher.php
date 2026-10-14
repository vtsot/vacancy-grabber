<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Infrastructure\Security;

use App\VacancyGrabber\Domain\User\PasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

final readonly class SymfonyPasswordHasher implements PasswordHasher
{
    public function __construct(
        private PasswordHasherFactoryInterface $passwordHasherFactory,
    ) {
    }

    public function hash(string $plainPassword): string
    {
        return $this->passwordHasherFactory->getPasswordHasher(SecurityUser::class)->hash($plainPassword);
    }
}
