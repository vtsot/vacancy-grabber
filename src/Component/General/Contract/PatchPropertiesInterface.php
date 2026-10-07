<?php

declare(strict_types=1);

namespace App\Component\General\Contract;

interface PatchPropertiesInterface
{
    public array $properties { get; }
    public array $fields { get; }
    public array $required { get; }
}
