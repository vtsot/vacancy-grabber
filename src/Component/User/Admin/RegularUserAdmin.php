<?php

declare(strict_types=1);

namespace App\Component\User\Admin;

use App\Component\User\Contract\Dictionary\UserRole;

class RegularUserAdmin extends AbstractUserAdmin
{
    public const bool MULTIPLE_ROLES           = false;
    public const bool USE_GOOGLE_AUTHENTICATOR = false;

    protected function resolveUserRoles(): array
    {
        return [
            UserRole::User->value,
        ];
    }
}
