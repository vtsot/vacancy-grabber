<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\User\Admin;

use App\VacancyGrabber\Domain\User\Entity\UserRole;

class SupervisorUserAdmin extends AbstractUserAdmin
{
    public const bool MULTIPLE_ROLES           = false;
    public const bool USE_GOOGLE_AUTHENTICATOR = true;

    protected function resolveUserRoles(): array
    {
        return [
            UserRole::Supervisor->value,
            UserRole::Admin->value,
        ];
    }
}
