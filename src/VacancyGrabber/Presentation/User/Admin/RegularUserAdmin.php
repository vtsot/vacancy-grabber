<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Presentation\User\Admin;

use App\VacancyGrabber\Domain\User\Entity\UserRole;

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
