<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\Shared\EnumValuesTrait;

enum UserRole: string
{
    use EnumValuesTrait;

    case Supervisor = 'ROLE_SUPERVISOR';
    case Admin      = 'ROLE_ADMIN';
    case User       = 'ROLE_USER';
}
