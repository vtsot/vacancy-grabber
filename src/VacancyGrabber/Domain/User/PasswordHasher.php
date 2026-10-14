<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\User;

interface PasswordHasher
{
    public function hash(string $plainPassword): string;
}
