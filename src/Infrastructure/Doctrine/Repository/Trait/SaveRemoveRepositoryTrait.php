<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository\Trait;

trait SaveRemoveRepositoryTrait
{
    public function save(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function remove(object $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }
}
