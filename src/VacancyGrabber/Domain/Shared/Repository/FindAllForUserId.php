<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Shared\Repository;

use Symfony\Component\Uid\Uuid;

interface FindAllForUserId
{
    public function findAllForUserId(?Uuid $userId = null, array $ids = []): array;
}
