<?php

declare(strict_types=1);

namespace App\Component\Company\Repository;

use App\Component\Company\Entity\Company;
use App\Component\General\Service\Util;
use App\Component\General\Shared\RepositoryTrait\FindByIdTrait;
use App\Component\General\Shared\RepositoryTrait\PaginatorRepositoryTrait;
use App\Component\General\Shared\RepositoryTrait\QueryConditionBuilderRepositoryTrait;
use App\Contract\Repository\FindByIdInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository implements FindByIdInterface
{
    use FindByIdTrait;
    use PaginatorRepositoryTrait;
    use QueryConditionBuilderRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function findOneByName(string $name): ?Company
    {
        $builder = $this->getBaseQuery(
            orderBy: 'name',
            orderDirection: 'asc',
            name: $name,
        );

        return $builder
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array<int, Company>
     */
    public function findAllForUserId(?Uuid $userId): array
    {
        $builder = $this->getBaseQuery(
            userId: $userId,
            orderBy: 'name',
            orderDirection: 'asc',
        );

        return $builder
            ->getQuery()
            ->getResult();
    }

    private function getBaseQuery(
        ?Uuid $userId = null,
        ?string $query = null,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        ?string $name = null,
    ): QueryBuilder {
        [$orderBy, $orderDirection, $orderTable] = $orderBy
            ? Util::normalizeOrderBy('q', $orderBy, $orderDirection)
            : [null, null, null];

        $builder = $this
            ->createQueryBuilder('q')
            ->select('q')
            ->groupBy('q.id');

        // when the order by is not q (not main table)
        // join the table
        if ($orderBy && $orderTable && $orderTable !== 'q') {
            $builder
                ->leftJoin("q.{$orderTable}", $orderTable);
        }

        if ($orderBy) {
            $builder->orderBy($orderBy, $orderDirection);
        }

        if ($userId) {
            $builder
                ->join('q.vacancies', 'v')
                ->andWhere('v.user = :userId')
                ->setParameter('userId', $userId->toBinary());
        }

        if ($query !== null) {
            $builder
                ->leftJoin('q.company', 'companyQuery')
                ->leftJoin('q.notes', 'noteQuery')
                ->groupBy('q.id');

            $this->buildQueryCondition($query, $builder, [
                'q.id',
                'q.name',
                'q.url',
                'noteQuery.description',
            ]);
        }

        if ($name !== null) {
            $builder
                ->andWhere('q.name = :name')
                ->setParameter('name', $name);
        }

        return $builder;
    }
}
