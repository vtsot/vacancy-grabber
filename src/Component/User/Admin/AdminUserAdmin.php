<?php

declare(strict_types=1);

namespace App\Component\User\Admin;

use App\Component\User\Contract\Dictionary\UserRole;

class AdminUserAdmin extends AbstractUserAdmin
{
    public const bool MULTIPLE_ROLES           = true;
    public const bool USE_GOOGLE_AUTHENTICATOR = true;

    protected function resolveUserRoles(): array
    {
        return [
            UserRole::Admin->value,
        ];
    }
}
