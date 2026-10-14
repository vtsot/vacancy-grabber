<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\GrabLog\Entity\GrabLog;
use App\Domain\GrabLog\Repository\GrabLogCriteria;
use App\Domain\GrabLog\Repository\GrabLogRepository;
use App\Domain\Shared\Repository\PaginatedResult;
use App\Infrastructure\Doctrine\Repository\Trait\FindByIdAndUserIdTrait;
use App\Infrastructure\Doctrine\Repository\Trait\FindByIdTrait;
use App\Infrastructure\Doctrine\Repository\Trait\OrderByRepositoryTrait;
use App\Infrastructure\Doctrine\Repository\Trait\PaginatorRepositoryTrait;
use App\Infrastructure\Doctrine\Repository\Trait\QueryConditionBuilderRepositoryTrait;
use App\Infrastructure\Doctrine\Repository\Trait\SaveRemoveRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class GrabLogDoctrineRepository extends ServiceEntityRepository implements GrabLogRepository
{
    use SaveRemoveRepositoryTrait;
    use OrderByRepositoryTrait;
    use FindByIdTrait;
    use FindByIdAndUserIdTrait;
    use PaginatorRepositoryTrait;
    use QueryConditionBuilderRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GrabLog::class);
    }

    private function createBaseQuery(GrabLogCriteria $criteria): QueryBuilder
    {
        [$orderBy, $orderDirection] = $criteria->orderBy
            ? $this->normalizeOrderBy('q', $criteria->orderBy, $criteria->orderDirection)
            : [null, null];

        $qb = $this
            ->createQueryBuilder('q')
            ->select('q');

        if ($orderBy) {
            $qb->orderBy($orderBy, $orderDirection);
        }

        if ($criteria->userId) {
            $qb
                ->andWhere('q.user = :userId')
                ->setParameter('userId', $criteria->userId->toBinary());
        }

        if ($criteria->query !== null) {
            $qb->groupBy('q.id');
            $this->buildQueryCondition($criteria->query, $qb, [
                'q.id',
                'q.description',
            ]);
        }

        if ($criteria->startDay !== null) {
            $qb
                ->andWhere('q.createdAt >= :startDay')
                ->setParameter('startDay', $criteria->startDay);
        }

        if ($criteria->endDay !== null) {
            $qb
                ->andWhere('q.createdAt <= :endDay')
                ->setParameter('endDay', $criteria->endDay);
        }

        if ($criteria->source !== null) {
            $qb
                ->andWhere('q.source = :source')
                ->setParameter('source', $criteria->source->toBinary());
        }

        return $qb;
    }

    public function getAllPaginated(GrabLogCriteria $criteria): PaginatedResult
    {
        return $this->paginate(
            $this->createBaseQuery($criteria),
            $criteria->page,
            $criteria->perPage,
            false,
        );
    }
}
