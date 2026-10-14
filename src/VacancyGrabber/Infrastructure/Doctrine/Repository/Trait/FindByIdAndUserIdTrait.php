<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Infrastructure\Doctrine\Repository\Trait;

use App\VacancyGrabber\Domain\Shared\EntityInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @method EntityInterface|null findOneBy(array $criteria, array $orderBy = null)
 */
trait FindByIdAndUserIdTrait
{
    public function findOneByIdAndUserId(Uuid $id, Uuid $userId): ?EntityInterface
    {
        return $this->findOneBy(['id' => $id, 'user' => $userId]);
    }
}
