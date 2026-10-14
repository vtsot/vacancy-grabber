<?php

declare(strict_types=1);

namespace App\Domain\Shared\Repository;

use App\Domain\Shared\EntityInterface;

interface FindOneByIdentifier
{
    public function findOneByIdentifier(string $identifier): ?EntityInterface;
}
