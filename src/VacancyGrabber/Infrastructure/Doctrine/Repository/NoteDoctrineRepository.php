<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Infrastructure\Doctrine\Repository;

use App\Component\General\Service\Util;
use App\VacancyGrabber\Application\Note\DTO\NoteCriteriaDTO;
use App\VacancyGrabber\Domain\Note\Entity\Note;
use App\VacancyGrabber\Domain\Note\Repository\NoteRepositoryInterface;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\FindByIdAndUserIdTrait;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\FindByIdTrait;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\PaginatorRepositoryTrait;
use App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine\QueryConditionBuilderRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\WindowPage;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class NoteDoctrineRepository extends ServiceEntityRepository implements NoteRepositoryInterface
{
    use FindByIdTrait;
    use FindByIdAndUserIdTrait;
    use PaginatorRepositoryTrait;
    use QueryConditionBuilderRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    //    public function findOneByName(string $name): ?Note
    //    {
    //        $criteria = new CompanyCriteriaDTO(orderBy: 'name', orderDirection: 'asc', name: $name);
    //        $qb       = $this->createBaseQuery($criteria);
    //
    //        return $qb
    //            ->setMaxResults(1)
    //            ->getQuery()
    //            ->getOneOrNullResult();
    // //    }
    //
    //    public function findAllForUserId(?Uuid $userId = null, array $ids = []): array
    //    {
    //        $criteria = new CompanyCriteriaDTO(userId: $userId, orderBy: 'name', orderDirection: 'asc', ids: $ids);
    //        $qb       = $this->createBaseQuery($criteria);
    //
    //        return $qb
    //            ->getQuery()
    //            ->getResult();
    //    }
    //
    public function createBaseQuery(NoteCriteriaDTO $criteria): QueryBuilder
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

        if ($criteria->userId) {
            $qb
                ->join('q.vacancies', 'v')
                ->andWhere('v.user = :userId')
                ->setParameter('userId', $criteria->userId->toBinary());
        }

        if ($criteria->query !== null) {
            $qb
                ->leftJoin('q.company', 'companyQuery')
                ->leftJoin('q.vacancy', 'vacancyQuery')
                //->leftJoin('vacancyQuery.company', 'vacancyCompanyQuery')
                ->groupBy('q.id');

            $this->buildQueryCondition($criteria->query, $qb, [
                'q.id',
                'q.description',
//                'vacancyCompanyQuery.name',
                'companyQuery.name',
            ]);
        }


//        if ($criteria->startDay !== null) {
//            $qb
//                ->andWhere('q.createdAt >= :startDay')
//                ->setParameter('startDay', $criteria->startDay);
//        }
//
//        if ($endDay !== null) {
//            $qb
//                ->andWhere('q.createdAt <= :endDay')
//                ->setParameter('endDay', $endDay);
//        }
//
//        if ($notedAtStartDay !== null) {
//            $qb
//                ->andWhere('q.notedAt >= :notedAtStartDay')
//                ->setParameter('notedAtStartDay', $notedAtStartDay);
//        }
//
//        if ($notedAtEndDay !== null) {
//            $qb
//                ->andWhere('q.notedAt <= :notedAtEndDay')
//                ->setParameter('notedAtEndDay', $notedAtEndDay);
//        }
//
//        if ($criteria->vacancyId !== null) {
//            $qb
//                ->andWhere('q.vacancy = :vacancyId')
//                ->setParameter('vacancyId', $criteria->vacancyId->toBinary());
//        }
//
//        if ($criteria->companyId !== null) {
//            $qb
//                ->leftJoin('q.vacancy', 'v')
//                ->leftJoin('v.company', 'c')
//                ->andWhere($builder->expr()->orX(...[
//                    'v.company = :companyId',
//                    'c.id = :companyId',
//                ]))
//                ->setParameter('companyId', $companyId->toBinary());
//        }

        return $qb;
    }

    public function getAllPaginated(NoteCriteriaDTO $criteria): WindowPage
    {
        return $this->getPaginator(
            $this->createBaseQuery($criteria),
            $criteria->page,
            $criteria->perPage,
            false,
        );
    }
}
