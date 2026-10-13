<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Infrastructure\Doctrine\Repository;

use App\VacancyGrabber\Application\Shared\Service\Util;
use App\VacancyGrabber\Application\Source\DTO\SourceCriteriaDTO;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\FindByIdAndUserIdTrait;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\FindByIdTrait;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\PaginatorRepositoryTrait;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\QueryConditionBuilderRepositoryTrait;
use App\VacancyGrabber\Domain\Source\Entity\Source;
use App\VacancyGrabber\Domain\Source\Repository\SourceRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\WindowPage;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class SourceDoctrineRepository extends ServiceEntityRepository implements SourceRepositoryInterface
{
    use FindByIdTrait;
    use FindByIdAndUserIdTrait;
    use PaginatorRepositoryTrait;
    use QueryConditionBuilderRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Source::class);
    }

    public function findAllSortByName(?Uuid $userId = null): array
    {
        return $this
            ->createBaseQuery(new SourceCriteriaDTO(userId: $userId, orderBy: 'name', orderDirection: 'asc'))
            ->getQuery()
            ->getResult();
    }

    public function createBaseQuery(SourceCriteriaDTO $criteria): QueryBuilder
    {
        [$orderBy, $orderDirection, $orderTable] = $criteria->orderBy
            ? Util::normalizeOrderBy('q', $criteria->orderBy, $criteria->orderDirection)
            : [null, null, null];

        $qb = $this
            ->createQueryBuilder('q')
            ->select('q');

        if ($criteria->userId) {
            $qb
                ->andWhere('q.user = :userId')
                ->setParameter('userId', $criteria->userId->toBinary());
        }

        // when the order by is not q (not main table)
        // join the table
        if ($orderBy && $orderTable && $orderTable !== 'q') {
            $qb
                ->leftJoin("q.{$orderTable}", $orderTable);
        }

        if ($orderBy) {
            $qb->orderBy($orderBy, $orderDirection);
        }

        if ($criteria->query !== null) {
            $qb->groupBy('q.id');
            $this->buildQueryCondition($criteria->query, $qb, [
                'q.id',
                'q.name',
                'q.url',
            ]);
        }

        if ($criteria->active !== null) {
            $qb
                ->andWhere('q.active = :active')
                ->setParameter('active', $criteria->active);
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

        return $qb;
    }

    public function getAllPaginated(SourceCriteriaDTO $criteria): WindowPage
    {
        return $this->getPaginator(
            $this->createBaseQuery($criteria),
            $criteria->page,
            $criteria->perPage,
            false,
        );
    }
}
