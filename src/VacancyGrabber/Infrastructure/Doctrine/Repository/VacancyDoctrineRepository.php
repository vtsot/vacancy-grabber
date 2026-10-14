<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Infrastructure\Doctrine\Repository;

use App\VacancyGrabber\Application\Component\Vacancy\DTO\VacancyCriteriaDTO;
use App\VacancyGrabber\Application\Shared\Util;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\FindByIdAndUserIdTrait;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\FindByIdTrait;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\PaginatorRepositoryTrait;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\QueryConditionBuilderRepositoryTrait;
use App\VacancyGrabber\Domain\Vacancy\Entity\Vacancy;
use App\VacancyGrabber\Domain\Vacancy\Entity\VacancyStatus;
use App\VacancyGrabber\Domain\Vacancy\Repository\VacancyRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\WindowPage;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class VacancyDoctrineRepository extends ServiceEntityRepository implements VacancyRepository
{
    use FindByIdTrait;
    use FindByIdAndUserIdTrait;
    use PaginatorRepositoryTrait;
    use QueryConditionBuilderRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vacancy::class);
    }

    public function findOneByUserIdAndSourceIdAndExternalId(
        Uuid $userId,
        Uuid $sourceId,
        string $externalId,
    ): ?Vacancy {
        return $this
            ->createBaseQuery(new VacancyCriteriaDTO(userId: $userId, externalId: $externalId, source: $sourceId))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array<string, array{total: int}>
     */
    public function countByStatus(?Uuid $userId): array
    {
        $builder = $this
            ->createBaseQuery(new VacancyCriteriaDTO(userId: $userId))
            ->select('q.status AS item_status')
            ->addSelect('COUNT(q.id) AS total')
            ->groupBy('item_status')
            ->orderBy('item_status', 'ASC');

        $data = $builder
            ->getQuery()
            ->getArrayResult();

        $result = [];

        foreach ($data as $item) {
            /** @var VacancyStatus $status */
            $status = $item['item_status'];

            $result[$status->value] = [
                'total' => $item['total'],
            ];
        }

        return $result;
    }

    /**
     * @return array<string, array{total: int}>
     */
    private function countByDateFiled(string $dateField, ?Uuid $userId = null): array
    {
        $builder = $this
            ->createBaseQuery(new VacancyCriteriaDTO(userId: $userId))
            ->select('DATE(q.' . $dateField . ') AS item_date')
            ->addSelect('COUNT(q.id) AS total')
            ->groupBy('item_date')
            ->orderBy('item_date', 'ASC');

        $data = $builder
            ->getQuery()
            ->getArrayResult();

        $result = [];

        foreach ($data as $item) {
            $result[$item['item_date']] = [
                'total' => $item['total'],
            ];
        }

        return $result;
    }

    /**
     * @return array<string, array{total: int}>
     */
    public function countByCreatedAt(?Uuid $userId = null): array
    {
        return $this->countByDateFiled('createdAt', $userId);
    }

    /**
     * @return array<string, array{total: int}>
     */
    public function countByResumeSentAt(?Uuid $userId = null): array
    {
        return $this->countByDateFiled('resumeSentAt', $userId);
    }

    /**
     * @return array<string, array{total: int}>
     */
    public function countByDeadLineAt(?Uuid $userId = null): array
    {
        return $this->countByDateFiled('deadLineAt', $userId);
    }

    public function findAllCompanyIdsByUserId(Uuid $userId): array
    {
        $builder = $this->createBaseQuery(new VacancyCriteriaDTO(userId: $userId))
            ->select('c.id AS companyId')
            ->join('q.company', 'c');

        $data   = $builder->getQuery()->getScalarResult();
        $result = [];

        foreach ($data as $item) {
            $result[] = Uuid::fromBinary($item['companyId']);
        }

        return $result;
    }

    public function createBaseQuery(VacancyCriteriaDTO $criteria): QueryBuilder
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

        if ($criteria->externalId) {
            $qb
                ->andWhere('q.externalId = :externalId')
                ->setParameter('externalId', $criteria->externalId);
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
            $qb
                // ->addSelect('company')
                ->leftJoin('q.company', 'companyQuery')
                ->leftJoin('q.notes', 'noteQuery')
                ->groupBy('q.id');

            $this->buildQueryCondition($criteria->query, $qb, [
                'q.id',
                'q.title',
                'q.description',
                'q.externalId',
                'q.url',
                'q.salary',
                'companyQuery.name',
                'noteQuery.description',
            ]);
        }

        if ($criteria->viewed !== null) {
            $qb
                ->andWhere('q.viewed = :viewed')
                ->setParameter('viewed', $criteria->viewed);
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

        if ($criteria->company !== null) {
            $qb
                ->andWhere('q.company = :company')
                ->setParameter('company', $criteria->company->toBinary());
        }

        if ($criteria->status !== null) {
            $qb
                ->andWhere('q.status = :status')
                ->setParameter('status', $criteria->status);
        }

        match ($criteria->isResumeSent) {
            true    => $qb->andWhere($qb->expr()->isNotNull('q.resumeSentAt')),
            false   => $qb->andWhere($qb->expr()->isNull('q.resumeSentAt')),
            default => static fn () => null,
        };

        match ($criteria->isDeadLine) {
            true    => $qb->andWhere($qb->expr()->isNotNull('q.deadLineAt')),
            false   => $qb->andWhere($qb->expr()->isNull('q.deadLineAt')),
            default => static fn () => null,
        };

        if ($criteria->resumeSentAt !== null) {
            $qb
                ->andWhere('q.resumeSentAt >= :resumeSentAtFrom')
                ->setParameter('resumeSentAtFrom', $criteria->resumeSentAt->startOfDay())
                ->andWhere('q.resumeSentAt <= :resumeSentAtTo')
                ->setParameter('resumeSentAtTo', $criteria->resumeSentAt->endOfDay());
        }

        if ($criteria->resumeSentAtStartDay !== null) {
            $qb
                ->andWhere('q.resumeSentAt >= :resumeSentAtStartDay')
                ->setParameter('resumeSentAtStartDay', $criteria->resumeSentAtStartDay->startOfDay());
        }

        if ($criteria->resumeSentAtEndDay !== null) {
            $qb
                ->andWhere('q.resumeSentAt <= :resumeSentAtEndDay')
                ->setParameter('resumeSentAtEndDay', $criteria->resumeSentAtEndDay->endOfDay());
        }

        if ($criteria->deadLineAt !== null) {
            $qb
                ->andWhere('q.deadLineAt >= :deadLineAtFrom')
                ->setParameter('deadLineAtFrom', $criteria->deadLineAt->startOfDay())
                ->andWhere('q.deadLineAt <= :deadLineAtTo')
                ->setParameter('deadLineAtTo', $criteria->deadLineAt->endOfDay());
        }

        if ($criteria->deadLineAtStartDay !== null) {
            $qb
                ->andWhere('q.deadLineAt >= :deadLineAtStartDay')
                ->setParameter('deadLineAtStartDay', $criteria->deadLineAtStartDay->startOfDay());
        }

        if ($criteria->deadLineAtEndDay !== null) {
            $qb
                ->andWhere('q.deadLineAt <= :deadLineAtEndDay')
                ->setParameter('deadLineAtEndDay', $criteria->deadLineAtEndDay->endOfDay());
        }

        return $qb;
    }

    public function getAllPaginated(VacancyCriteriaDTO $criteria): WindowPage
    {
        return $this->getPaginator(
            $this->createBaseQuery($criteria),
            $criteria->page,
            $criteria->perPage,
            false,
        );
    }
}
