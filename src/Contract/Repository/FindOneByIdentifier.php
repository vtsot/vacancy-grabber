<?php

declare(strict_types=1);

namespace App\Contract\Repository;

use App\Contract\Entity\EntityInterface;

interface FindOneByIdentifier
{
    public function findOneByIdentifier(string $identifier): ?EntityInterface;
}
