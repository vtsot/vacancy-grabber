<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Shared\Repository;

use App\VacancyGrabber\Domain\Shared\EntityInterface;
use Symfony\Component\Uid\Uuid;

interface FindOneByIdAndUserId
{
    public function findOneByIdAndUserId(Uuid $id, Uuid $userId): ?EntityInterface;
}
