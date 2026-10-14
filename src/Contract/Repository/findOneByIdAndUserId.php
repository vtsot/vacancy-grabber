<?php

declare(strict_types=1);

namespace App\Contract\Repository;

use App\Contract\Entity\EntityInterface;
use Symfony\Component\Uid\Uuid;

interface findOneByIdAndUserId
{
    public function findOneByIdAndUserId(Uuid $id, Uuid $userId): ?EntityInterface;
}
