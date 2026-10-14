<?php

declare(strict_types=1);

namespace App\Contract\Repository;

use Symfony\Component\Uid\Uuid;

interface findAllForUserId
{
    public function findAllForUserId(?Uuid $userId = null, array $ids = []): array;
}
