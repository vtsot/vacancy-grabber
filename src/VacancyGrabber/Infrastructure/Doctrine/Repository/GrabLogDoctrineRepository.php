<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Infrastructure\Doctrine\Repository;

use App\VacancyGrabber\Application\GrabLog\DTO\GrabLogCriteriaDTO;
use App\VacancyGrabber\Application\Shared\Service\Util;
use App\VacancyGrabber\Domain\GrabLog\Entity\GrabLog;
use App\VacancyGrabber\Domain\GrabLog\Repository\GrabLogRepositoryInterface;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\FindByIdAndUserIdTrait;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\FindByIdTrait;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\PaginatorRepositoryTrait;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\QueryConditionBuilderRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\WindowPage;
use Doctrine\Persistence\ManagerRegistry;

class GrabLogDoctrineRepository extends ServiceEntityRepository implements GrabLogRepositoryInterface
{
    use FindByIdTrait;
    use FindByIdAndUserIdTrait;
    use PaginatorRepositoryTrait;
    use QueryConditionBuilderRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GrabLog::class);
    }

    public function createBaseQuery(GrabLogCriteriaDTO $criteria): QueryBuilder
    {
        [$orderBy, $orderDirection] = $criteria->orderBy
            ? Util::normalizeOrderBy('q', $criteria->orderBy, $criteria->orderDirection)
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

    public function getAllPaginated(GrabLogCriteriaDTO $criteria): WindowPage
    {
        return $this->getPaginator(
            $this->createBaseQuery($criteria),
            $criteria->page,
            $criteria->perPage,
            false,
        );
    }
}
