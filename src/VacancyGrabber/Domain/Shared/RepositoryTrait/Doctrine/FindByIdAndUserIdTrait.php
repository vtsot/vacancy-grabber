<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine;

use App\Contract\Entity\EntityInterface;
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
