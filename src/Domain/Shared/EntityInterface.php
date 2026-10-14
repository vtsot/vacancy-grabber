<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use Symfony\Component\Uid\Uuid;

interface EntityInterface
{
    public function getId(): ?Uuid;
}
