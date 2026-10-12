<?php

declare(strict_types=1);

namespace App\Component\User\Repository;

use App\Component\User\Entity\User;
use App\Contract\Repository\findOneById;
use App\Contract\Repository\findOneByIdentifier;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\FindByIdTrait;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\PaginatorRepositoryTrait;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\QueryConditionBuilderRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements findOneById, findOneByIdentifier
{
    use FindByIdTrait;
    use PaginatorRepositoryTrait;
    use QueryConditionBuilderRepositoryTrait;

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
