<?php

declare(strict_types=1);

namespace App\Component\Source\Repository;

use App\Component\Common\Service\Util;
use App\Component\Source\Entity\Source;
use App\Contract\Repository\FindByIdInterface;
use App\Repository\Traits\PaginatorTrait;
use App\Repository\Traits\QueryConditionBuilderTrait;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @method Source|null find($id, $lockMode = null, $lockVersion = null)
 * @method Source|null findOneBy(array $criteria, array $orderBy = null)
 * @method Source[]    findAll()
 * @method Source[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SourceRepository extends ServiceEntityRepository implements FindByIdInterface
{
    use PaginatorTrait;
    use QueryConditionBuilderTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Source::class);
    }

    public function findOneById(Uuid $id): ?Source
    {
        return $this->find($id);
    }

    /**
     * @return Paginator<Source>
     */
    public function getAllPaginated(
        int $page,
        int $perPage,
        ?string $query = null,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        ?bool $active = null,
        ?CarbonImmutable $startDay = null,
        ?CarbonImmutable $endDay = null,
    ): Paginator {
        $qb = $this->getBaseQuery(
            $query,
            $orderBy,
            $orderDirection,
            $active,
            $startDay,
            $endDay,
        );

        return $this->getPaginator($qb, $page, $perPage, false);
    }

    private function getBaseQuery(
        ?string $query = null,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        ?bool $active = null,
        ?CarbonImmutable $startDay = null,
        ?CarbonImmutable $endDay = null,
    ): QueryBuilder {
        [$orderBy, $orderDirection] = $orderBy ? Util::normalizeOrderBy('q', $orderBy, $orderDirection) : [null, null];

        $qb = $this->createQueryBuilder('q')
            ->select('q');

        if ($orderBy) {
            $qb->orderBy($orderBy, $orderDirection);
        }

        if ($query !== null) {
            $qb->groupBy('q.id');
            $this->buildQueryCondition($query, $qb, [
                'q.id',
                'q.name',
                'q.url',
            ]);
        }

        if ($active !== null) {
            $qb->andWhere('q.active = :active')
                ->setParameter('active', $active);
        }

        if ($startDay !== null) {
            $qb->andWhere('q.createdAt >= :startDay')
                ->setParameter('startDay', $startDay);
        }

        if ($endDay !== null) {
            $qb->andWhere('q.createdAt <= :endDay')
                ->setParameter('endDay', $endDay);
        }

        return $qb;
    }
}
