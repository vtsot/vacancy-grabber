<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Infrastructure\Doctrine\Repository;

use App\VacancyGrabber\Application\Company\DTO\CompanyCriteriaDTO;
use App\VacancyGrabber\Application\Shared\Service\Util;
use App\VacancyGrabber\Domain\Company\Entity\Company;
use App\VacancyGrabber\Domain\Company\Repository\CompanyRepositoryInterface;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\FindByIdAndUserIdTrait;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\FindByIdTrait;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\PaginatorRepositoryTrait;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\QueryConditionBuilderRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\WindowPage;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class CompanyDoctrineRepository extends ServiceEntityRepository implements CompanyRepositoryInterface
{
    use FindByIdTrait;
    use FindByIdAndUserIdTrait;
    use PaginatorRepositoryTrait;
    use QueryConditionBuilderRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function findOneByName(string $name): ?Company
    {
        $criteria = new CompanyCriteriaDTO(orderBy: 'name', orderDirection: 'asc', name: $name);
        $qb       = $this->createBaseQuery($criteria);

        return $qb
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllForUserId(?Uuid $userId = null, array $ids = []): array
    {
        $criteria = new CompanyCriteriaDTO(userId: $userId, orderBy: 'name', orderDirection: 'asc', ids: $ids);
        $qb       = $this->createBaseQuery($criteria);

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function createBaseQuery(CompanyCriteriaDTO $criteria): QueryBuilder
    {
        [$orderBy, $orderDirection, $orderTable] = $criteria->orderBy
            ? Util::normalizeOrderBy('q', $criteria->orderBy, $criteria->orderDirection)
            : [null, null, null];

        $qb = $this
            ->createQueryBuilder('q')
            ->select('q')
            ->groupBy('q.id');

        //        return $qb;

        // when the order by is not q (not main table)
        // join the table
        if ($orderBy && $orderTable && $orderTable !== 'q') {
            $qb
                ->leftJoin("q.{$orderTable}", $orderTable);
        }

        if ($orderBy) {
            $qb->orderBy($orderBy, $orderDirection);
        }

        //        if ($criteria->userId) {
        //            //            $qb
        //            //                ->join('q.vacancies', 'v')
        //            //                ->andWhere('v.user = :userId')
        //            //                ->setParameter('userId', $criteria->userId->toBinary());
        //        }

        if ($criteria->query !== null) {
            $qb
                // ->leftJoin('q.company', 'companyQuery')
                ->leftJoin('q.notes', 'noteQuery')
                ->groupBy('q.id');

            $this->buildQueryCondition($criteria->query, $qb, [
                'q.id',
                'q.name',
                'q.url',
                // 'noteQuery.description',
            ]);
        }

        if ($criteria->ids !== null) {
            // todo: add into ids nill id
            $qb
                ->andWhere($qb->expr()->in('q.id', ':companyIds'))
                ->setParameter('companyIds', array_map(static fn (Uuid $uuid) => $uuid->toBinary(), $criteria->ids));
        }

        if ($criteria->name !== null) {
            $qb
                ->andWhere('q.name = :name')
                ->setParameter('name', $criteria->name);
        }

        return $qb;
    }

    public function getAllPaginated(CompanyCriteriaDTO $criteria): WindowPage
    {
        return $this->getPaginator(
            $this->createBaseQuery($criteria),
            $criteria->page,
            $criteria->perPage,
            false,
        );
    }
}
