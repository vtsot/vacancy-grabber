<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Shared\Contract;

interface PatchPropertiesInterface
{
    public array $properties { get; }
    public array $fields { get; }
    public array $required { get; }
}
