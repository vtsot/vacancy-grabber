<?php

declare(strict_types=1);

namespace App\Component\GrabLog\Repository;

use App\Component\General\Service\Util;
use App\Component\General\Shared\RepositoryTrait\FindByIdAndUserIdTrait;
use App\Component\General\Shared\RepositoryTrait\FindByIdTrait;
use App\Component\General\Shared\RepositoryTrait\PaginatorRepositoryTrait;
use App\Component\General\Shared\RepositoryTrait\QueryConditionBuilderRepositoryTrait;
use App\Component\GrabLog\Entity\GrabLog;
use App\Contract\Repository\FindByIdAndUserIdInterface;
use App\Contract\Repository\FindByIdInterface;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\WindowPage;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @method GrabLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method GrabLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method GrabLog[]    findAll()
 * @method GrabLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GrabLogRepository extends ServiceEntityRepository implements FindByIdInterface, FindByIdAndUserIdInterface
{
    use FindByIdTrait;
    use FindByIdAndUserIdTrait;
    use PaginatorRepositoryTrait;
    use QueryConditionBuilderRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GrabLog::class);
    }

    private function getBaseQuery(
        ?Uuid $userId,
        ?string $query = null,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        ?CarbonImmutable $startDay = null,
        ?CarbonImmutable $endDay = null,
        ?Uuid $source = null,
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
                'q.name',
            ]);
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

        return $builder;
    }

    public function getAllPaginated(
        ?Uuid $userId,
        int $page,
        int $perPage,
        ?string $query = null,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        ?CarbonImmutable $startDay = null,
        ?CarbonImmutable $endDay = null,
        ?Uuid $source = null,
    ): WindowPage {
        $builder = $this->getBaseQuery(
            userId: $userId,
            query: $query,
            orderBy: $orderBy,
            orderDirection: $orderDirection,
            startDay: $startDay,
            endDay: $endDay,
            source: $source,
        );

        return $this->getPaginator($builder, $page, $perPage, false);
    }
}
