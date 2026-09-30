<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Repository;

use App\Component\General\Service\Util;
use App\Component\General\Shared\RepositoryTrait\PaginatorRepositoryTrait;
use App\Component\General\Shared\RepositoryTrait\QueryConditionBuilderRepositoryTrait;
use App\Component\Vacancy\Contract\Dictionary\VacancyStatus;
use App\Component\Vacancy\Entity\Vacancy;
use App\Contract\Repository\FindByIdInterface;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\WindowPage;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @method Vacancy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vacancy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vacancy[]    findAll()
 * @method Vacancy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VacancyRepository extends ServiceEntityRepository implements FindByIdInterface
{
    use PaginatorRepositoryTrait;
    use QueryConditionBuilderRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vacancy::class);
    }

    public function findOneById(Uuid $id): ?Vacancy
    {
        return $this->find($id);
    }

    public function findOneBySourceIdAndExternalId(Uuid $sourceId, string $externalId): ?Vacancy
    {
        return $this->findOneBy(['source' => $sourceId, 'externalId' => $externalId]);
    }

    /**
     * @return array<string, array{total: int}>
     */
    private function countByDateFiled(string $dateField): array
    {
        $builder = $this
            ->createQueryBuilder('q')
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
    public function countByCreatedAt(): array
    {
        return $this->countByDateFiled('createdAt');
    }

    /**
     * @return array<string, array{total: int}>
     */
    public function countByResumeSentAt(): array
    {
        return $this->countByDateFiled('resumeSentAt');
    }

    private function getBaseQuery(
        ?string $query = null,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        ?bool $viewed = null,
        ?CarbonImmutable $startDay = null,
        ?CarbonImmutable $endDay = null,
        ?Uuid $source = null,
        ?Uuid $company = null,
        ?VacancyStatus $status = null,
        ?bool $isResumeSent = null,
        ?CarbonImmutable $resumeSentAt = null,
        ?CarbonImmutable $resumeSentAtStartDay = null,
        ?CarbonImmutable $resumeSentAtEndDay = null,
    ): QueryBuilder {
        [$orderBy, $orderDirection, $orderTable] = $orderBy
            ? Util::normalizeOrderBy('q', $orderBy, $orderDirection)
            : [null, null, null];

        $builder = $this
            ->createQueryBuilder('q')
            ->select('q');

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
            $builder
                // ->addSelect('company')
                ->leftJoin('q.company', 'companyQuery')
                ->leftJoin('q.notes', 'noteQuery')
                ->groupBy('q.id');

            $this->buildQueryCondition($query, $builder, [
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

        if ($viewed !== null) {
            $builder
                ->andWhere('q.viewed = :viewed')
                ->setParameter('viewed', $viewed);
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

        if ($source !== null) {
            $builder
                ->andWhere('q.source = :source')
                ->setParameter('source', $source->toBinary());
        }

        if ($company !== null) {
            $builder
                ->andWhere('q.company = :company')
                ->setParameter('company', $company->toBinary());
        }

        if ($status !== null) {
            $builder
                ->andWhere('q.status = :status')
                ->setParameter('status', $status);
        }

        match ($isResumeSent) {
            true    => $builder->andWhere($builder->expr()->isNotNull('q.resumeSentAt')),
            false   => $builder->andWhere($builder->expr()->isNull('q.resumeSentAt')),
            default => static fn () => null,
        };

        if ($resumeSentAt !== null) {
            $builder
                ->andWhere('q.resumeSentAt >= :resumeSentAtFrom')
                ->setParameter('resumeSentAtFrom', $resumeSentAt->startOfDay())
                ->andWhere('q.resumeSentAt <= :resumeSentAtTo')
                ->setParameter('resumeSentAtTo', $resumeSentAt->endOfDay());
        }

        if ($resumeSentAtStartDay !== null) {
            $builder
                ->andWhere('q.createdAt >= :resumeSentAtStartDay')
                ->setParameter('resumeSentAtStartDay', $resumeSentAtStartDay->startOfDay());
        }

        if ($resumeSentAtEndDay !== null) {
            $builder
                ->andWhere('q.createdAt <= :resumeSentAtEndDay')
                ->setParameter('resumeSentAtEndDay', $resumeSentAtEndDay->endOfDay());
        }

        return $builder;
    }

    public function getAllPaginated(
        int $page,
        int $perPage,
        ?string $query = null,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        ?bool $viewed = null,
        ?CarbonImmutable $startDay = null,
        ?CarbonImmutable $endDay = null,
        ?Uuid $source = null,
        ?Uuid $company = null,
        ?VacancyStatus $status = null,
        ?bool $isResumeSent = null,
        ?CarbonImmutable $resumeSentAt = null,
        ?CarbonImmutable $resumeSentAtStartDay = null,
        ?CarbonImmutable $resumeSentAtEndDay = null,
    ): WindowPage {
        $builder = $this->getBaseQuery(
            $query,
            $orderBy,
            $orderDirection,
            $viewed,
            $startDay,
            $endDay,
            $source,
            $company,
            $status,
            $isResumeSent,
            $resumeSentAt,
            $resumeSentAtStartDay,
            $resumeSentAtEndDay,
        );

        return $this->getPaginator($builder, $page, $perPage, false);
    }
}
