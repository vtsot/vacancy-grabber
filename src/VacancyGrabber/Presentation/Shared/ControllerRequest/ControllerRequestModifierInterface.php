<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Presentation\Shared\ControllerRequest;

interface ControllerRequestModifierInterface
{
    public function modify(mixed $value): mixed;
}
