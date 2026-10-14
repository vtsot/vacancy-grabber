<?php

declare(strict_types=1);

namespace App\Domain\Shared\Repository;

use App\Domain\Shared\EntityInterface;
use Symfony\Component\Uid\Uuid;

interface FindOneById
{
    public function findOneById(Uuid $id): ?EntityInterface;
}
