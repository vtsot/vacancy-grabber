<?php

declare(strict_types=1);

namespace App\Component\General\Shared\RepositoryTrait;

use App\Contract\Entity\EntityInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @method EntityInterface|null find($id, $lockMode = null, $lockVersion = null)
 */
trait FindByIdTrait
{
    public function findOneById(Uuid $id): ?EntityInterface
    {
        return $this->find($id);
    }
}
