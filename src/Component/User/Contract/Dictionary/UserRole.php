<?php

declare(strict_types=1);

namespace App\Component\User\Contract\Dictionary;

use App\Component\General\Shared\EnumValuesTrait;

enum UserRole: string
{
    use EnumValuesTrait;

    case Supervisor = 'ROLE_SUPERVISOR';
    case User       = 'ROLE_USER';
    case Admin      = 'ROLE_ADMIN';
}
