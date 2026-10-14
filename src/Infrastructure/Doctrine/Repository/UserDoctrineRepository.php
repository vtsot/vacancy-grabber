<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepository;
use App\Infrastructure\Doctrine\Repository\Trait\FindByIdTrait;
use App\Infrastructure\Doctrine\Repository\Trait\SaveRemoveRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class UserDoctrineRepository extends ServiceEntityRepository implements UserRepository
{
    use SaveRemoveRepositoryTrait;
    use FindByIdTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneByIdentifier(string $identifier): ?User
    {
        try {
            return $this->createQueryBuilder('u')
                ->where('u.email = :email')
                ->setParameter('email', $identifier)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            return null;
        }
    }
}
