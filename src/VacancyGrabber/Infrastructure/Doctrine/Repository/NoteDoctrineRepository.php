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

use function array_map;

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

    public function findOneByUserIdAndNoteIdAndVacancyId(Uuid $userId, Uuid $noteId, Uuid $vacancyId): ?Note
    {
        return $this->createBaseQuery(new NoteCriteriaDTO(userId: $userId, vacancyId: $vacancyId))
            ->andWhere('q.id = :noteId')
            ->setParameter('noteId', $noteId->toBinary())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function countByVacancyIds(?Uuid $userId, array $vacancyIds): array
    {
        $qb = $this->createBaseQuery(new NoteCriteriaDTO(userId: $userId));
        $qb
            ->select('IDENTITY(q.vacancy) as id', 'COUNT(q.id) as total')
            ->join('q.vacancy', 'v')
            ->andWhere($qb->expr()->in('v.id', ':vacancyIds'))
            ->setParameter('vacancyIds', array_map(static fn (Uuid $uuid) => $uuid->toBinary(), $vacancyIds))
            ->groupBy('v.id');

        $result = [];

        foreach ($qb->getQuery()->getScalarResult() as $item) {
            $result[Uuid::fromBinary($item['id'])->toString()] = [
                'total' => $item['total'],
            ];
        }

        return $result;
    }

    public function findAllCompanyIdsByUserId(Uuid $userId): array
    {
        $qb = $this->createBaseQuery(new NoteCriteriaDTO(userId: $userId))
            ->select('c.id AS companyId')
            ->join('q.vacancy', 'v')
            ->join('v.company', 'c');

        $result = [];

        foreach ($qb->getQuery()->getScalarResult() as $item) {
            $result[] = Uuid::fromBinary($item['companyId']);
        }

        return $result;
    }

    public function createBaseQuery(NoteCriteriaDTO $criteria): QueryBuilder
    {
        [$orderBy, $orderDirection, $orderTable] = $criteria->orderBy
            ? Util::normalizeOrderBy('q', $criteria->orderBy, $criteria->orderDirection)
            : [null, null, null];

        $qb = $this
            ->createQueryBuilder('q')
            ->select('q')
            ->groupBy('q.id');

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
                ->andWhere('q.user = :userId')
                ->setParameter('userId', $criteria->userId->toBinary());
        }

        if ($criteria->query !== null) {
            $qb
                ->leftJoin('q.company', 'companyQuery')
                ->leftJoin('q.vacancy', 'vacancyQuery')
                ->leftJoin('vacancyQuery.company', 'vacancyCompanyQuery');

            $this->buildQueryCondition($criteria->query, $qb, [
                'q.id',
                'q.description',
                'vacancyCompanyQuery.name',
                'companyQuery.name',
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

        if ($criteria->notedAtStartDay !== null) {
            $qb
                ->andWhere('q.notedAt >= :notedAtStartDay')
                ->setParameter('notedAtStartDay', $criteria->notedAtStartDay);
        }

        if ($criteria->notedAtEndDay !== null) {
            $qb
                ->andWhere('q.notedAt <= :notedAtEndDay')
                ->setParameter('notedAtEndDay', $criteria->notedAtEndDay);
        }

        if ($criteria->vacancyId !== null) {
            $qb
                ->andWhere('q.vacancy = :vacancyId')
                ->setParameter('vacancyId', $criteria->vacancyId->toBinary());
        }

        if ($criteria->companyId !== null) {
            $qb
                ->leftJoin('q.vacancy', 'v')
                ->andWhere('v.company = :companyId')
                ->setParameter('companyId', $criteria->companyId->toBinary());
        }

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
