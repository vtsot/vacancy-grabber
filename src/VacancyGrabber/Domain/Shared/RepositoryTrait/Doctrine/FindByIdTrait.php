<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine;

use App\Contract\Entity\EntityInterface;
use Symfony\Component\Uid\Uuid;

trait FindByIdTrait
{
    public function findOneById(Uuid $id): ?EntityInterface
    {
        return $this->find($id);
    }
}
