<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Domain\Shared\Repository\FindOneById;
use App\Domain\Shared\Repository\FindOneByIdentifier;
use App\Domain\User\Entity\User;
use Symfony\Component\Uid\Uuid;

/**
 * @method User|null findOneById(Uuid $id)
 * @method User|null findOneByIdentifier(string $identifier)
 */
interface UserRepository extends FindOneById, FindOneByIdentifier
{
    public function save(User $user): void;

    public function remove(User $user): void;
}
