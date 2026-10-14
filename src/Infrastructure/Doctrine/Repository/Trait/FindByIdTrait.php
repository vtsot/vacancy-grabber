<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository\Trait;

use App\Domain\Shared\EntityInterface;
use Symfony\Component\Uid\Uuid;

trait FindByIdTrait
{
    public function findOneById(Uuid $id): ?EntityInterface
    {
        return $this->find($id);
    }
}
