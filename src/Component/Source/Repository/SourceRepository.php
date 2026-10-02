<?php

declare(strict_types=1);

namespace App\Component\Source\Repository;

use App\Component\General\Service\Util;
use App\Component\General\Shared\RepositoryTrait\FindByIdAndUserIdTrait;
use App\Component\General\Shared\RepositoryTrait\FindByIdTrait;
use App\Component\General\Shared\RepositoryTrait\PaginatorRepositoryTrait;
use App\Component\General\Shared\RepositoryTrait\QueryConditionBuilderRepositoryTrait;
use App\Component\Source\Entity\Source;
use App\Contract\Repository\FindByIdAndUserIdInterface;
use App\Contract\Repository\FindByIdInterface;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\WindowPage;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @method Source|null findOneById(Uuid $id)
 * @method Source|null findOneByIdAndUserId(Uuid $id, Uuid $userId)
 * @method Source|null find($id, $lockMode = null, $lockVersion = null)
 * @method Source|null findOneBy(array $criteria, array $orderBy = null)
 * @method Source[]    findAll()
 * @method Source[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SourceRepository extends ServiceEntityRepository implements FindByIdInterface, FindByIdAndUserIdInterface
{
    use FindByIdTrait;
    use FindByIdAndUserIdTrait;
    use PaginatorRepositoryTrait;
    use QueryConditionBuilderRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Source::class);
    }

    /**
     * @return array<int, Source>
     */
    public function findAllSortByName(
        ?Uuid $userId = null,
    ): array {
        return $this
            ->getBaseQuery(userId: $userId, orderBy: 'name', orderDirection: 'asc')
            ->getQuery()
            ->getResult();
    }

    private function getBaseQuery(
        ?Uuid $userId = null,
        ?string $query = null,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        ?bool $active = null,
        ?CarbonImmutable $startDay = null,
        ?CarbonImmutable $endDay = null,
    ): QueryBuilder {
        [$orderBy, $orderDirection, $orderTable] = $orderBy
            ? Util::normalizeOrderBy('q', $orderBy, $orderDirection)
            : [null, null, null];

        $builder = $this
            ->createQueryBuilder('q')
            ->select('q');

        if ($userId) {
            $builder
                ->andWhere('q.user = :userId')
                ->setParameter('userId', $userId->toBinary());
        }

        // when the order by is not q (not main table)
        // join the table
        if ($orderBy && $orderTable && $orderTable !== 'q') {
            $builder
                ->leftJoin("q.{$orderTable}", $orderTable);
        }

        if ($orderBy) {
            $builder->orderBy($orderBy, $orderDirection);
        }

        if ($query !== null) {
            $builder->groupBy('q.id');
            $this->buildQueryCondition($query, $builder, [
                'q.id',
                'q.name',
                'q.url',
            ]);
        }

        if ($active !== null) {
            $builder->andWhere('q.active = :active')
                ->setParameter('active', $active);
        }

        if ($startDay !== null) {
            $builder
                ->andWhere('q.createdAt >= :startDay')
                ->setParameter('startDay', $startDay);
        }

        if ($endDay !== null) {
            $builder
                ->andWhere('q.createdAt <= :endDay')
                ->setParameter('endDay', $endDay);
        }

        return $builder;
    }

    public function getAllPaginated(
        ?Uuid $userId,
        ?int $page,
        ?int $perPage,
        ?string $query = null,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        ?bool $active = null,
        ?CarbonImmutable $startDay = null,
        ?CarbonImmutable $endDay = null,
    ): WindowPage {
        $builder = $this->getBaseQuery(
            userId: $userId,
            query: $query,
            orderBy: $orderBy,
            orderDirection: $orderDirection,
            active: $active,
            startDay:  $startDay,
            endDay: $endDay,
        );

        return $this->getPaginator($builder, $page, $perPage, false);
    }
}
