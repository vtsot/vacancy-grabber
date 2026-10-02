<?php

declare(strict_types=1);

namespace App\Component\General\Shared\RepositoryTrait;

use App\Component\Source\Entity\Source;
use App\Contract\Entity\EntityInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @method EntityInterface|null findOneBy(array $criteria, array $orderBy = null)
 */
trait FindByIdAndUserIdTrait
{
    public function findOneByIdAndUserId(Uuid $id, Uuid $userId): ?Source
    {
        return $this->findOneBy(['id' => $id, 'user' => $userId]);
    }
}
