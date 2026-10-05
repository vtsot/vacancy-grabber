<?php

declare(strict_types=1);

namespace App\Component\Note\Repository;

use App\Component\General\Service\Util;
use App\Component\General\Shared\RepositoryTrait\FindByIdAndUserIdTrait;
use App\Component\General\Shared\RepositoryTrait\FindByIdTrait;
use App\Component\General\Shared\RepositoryTrait\PaginatorRepositoryTrait;
use App\Component\General\Shared\RepositoryTrait\QueryConditionBuilderRepositoryTrait;
use App\Component\Note\Entity\Note;
use App\Contract\Repository\FindByIdAndUserIdInterface;
use App\Contract\Repository\FindByIdInterface;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\WindowPage;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

use function array_map;

/**
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository implements FindByIdInterface, FindByIdAndUserIdInterface
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
        return $this->getBaseQuery(userId: $userId, vacancyId: $vacancyId)
            ->andWhere('q.id = :noteId')
            ->setParameter('noteId', $noteId->toBinary())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Uuid[] $vacancyIds
     *
     * @return array<string, array{total: int}>
     */
    public function countByVacancyIds(?Uuid $userId, array $vacancyIds): array
    {
        $builder = $this->getBaseQuery(userId: $userId);
        $builder
            ->select('IDENTITY(q.vacancy) as id', 'COUNT(q.id) as total')
            ->join('q.vacancy', 'v')
            ->andWhere($builder->expr()->in('v.id', ':vacancyIds'))
            ->setParameter('vacancyIds', array_map(static fn (Uuid $uuid) => $uuid->toBinary(), $vacancyIds))
            ->groupBy('v.id');

        $data = $builder
            ->getQuery()
            ->getScalarResult();

        $result = [];

        foreach ($data as $item) {
            $result[Uuid::fromBinary($item['id'])->toString()] = [
                'total' => $item['total'],
            ];
        }

        return $result;
    }

    private function getBaseQuery(
        ?Uuid $userId = null,
        ?string $query = null,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        ?bool $active = null,
        ?CarbonImmutable $startDay = null,
        ?CarbonImmutable $endDay = null,
        ?CarbonImmutable $notedAtStartDay = null,
        ?CarbonImmutable $notedAtEndDay = null,
        ?Uuid $vacancyId = null,
        ?Uuid $companyId = null,
    ): QueryBuilder {
        [$orderBy, $orderDirection] = $orderBy ? Util::normalizeOrderBy('q', $orderBy, $orderDirection) : [null, null];

        $builder = $this->createQueryBuilder('q')
            ->select('q');

        if ($orderBy) {
            $builder->orderBy($orderBy, $orderDirection);
        }

        if ($userId) {
            $builder
                ->andWhere('q.user = :userId')
                ->setParameter('userId', $userId->toBinary());
        }

        if ($query !== null) {
            $builder->groupBy('q.id');
            $this->buildQueryCondition($query, $builder, [
                'q.id',
                'q.description',
            ]);
        }

        if ($active !== null) {
            $builder
                ->andWhere('q.active = :active')
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

        if ($notedAtStartDay !== null) {
            $builder
                ->andWhere('q.notedAt >= :notedAtStartDay')
                ->setParameter('notedAtStartDay', $notedAtStartDay);
        }

        if ($notedAtEndDay !== null) {
            $builder
                ->andWhere('q.notedAt <= :notedAtEndDay')
                ->setParameter('notedAtEndDay', $notedAtEndDay);
        }

        if ($vacancyId !== null) {
            $builder
                ->andWhere('q.vacancy = :vacancyId')
                ->setParameter('vacancyId', $vacancyId->toBinary());
        }

        if ($companyId !== null) {
            $builder
                ->andWhere('q.company = :companyId')
                ->setParameter('companyId', $companyId->toBinary());
        }

        return $builder;
    }

    public function getAllPaginated(
        ?Uuid $userId,
        int $page,
        int $perPage,
        ?string $query = null,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        ?bool $active = null,
        ?CarbonImmutable $startDay = null,
        ?CarbonImmutable $endDay = null,
        ?CarbonImmutable $notedAtStartDay = null,
        ?CarbonImmutable $notedAtEndDay = null,
        ?Uuid $vacancyId = null,
        ?Uuid $companyId = null,
    ): WindowPage {
        $builder = $this->getBaseQuery(
            userId: $userId,
            query: $query,
            orderBy: $orderBy,
            orderDirection: $orderDirection,
            active: $active,
            startDay: $startDay,
            endDay: $endDay,
            notedAtStartDay: $notedAtStartDay,
            notedAtEndDay: $notedAtEndDay,
            vacancyId: $vacancyId,
            companyId: $companyId,
        );

        return $this->getPaginator($builder, $page, $perPage, false);
    }
}
