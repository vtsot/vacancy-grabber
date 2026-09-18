<?php

declare(strict_types=1);

namespace App\Component\Source\Repository;

use App\Component\General\Service\Util;
use App\Component\General\Shared\RepositoryTrait\PaginatorRepositoryTrait;
use App\Component\General\Shared\RepositoryTrait\QueryConditionBuilderRepositoryTrait;
use App\Component\Source\Entity\Source;
use App\Contract\Repository\FindByIdInterface;
use Carbon\CarbonImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\WindowPage;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @method Source|null find($id, $lockMode = null, $lockVersion = null)
 * @method Source|null findOneBy(array $criteria, array $orderBy = null)
 * @method Source[]    findAll()
 * @method Source[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SourceRepository extends ServiceEntityRepository implements FindByIdInterface
{
    use PaginatorRepositoryTrait;
    use QueryConditionBuilderRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Source::class);
    }

    public function findOneById(Uuid $id): ?Source
    {
        return $this->find($id);
    }

    public function getAllPaginated(
        int $page,
        int $perPage,
        ?string $query = null,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        ?bool $active = null,
        ?CarbonImmutable $startDay = null,
        ?CarbonImmutable $endDay = null,
    ): WindowPage {
        $builder = $this->getBaseQuery(
            $query,
            $orderBy,
            $orderDirection,
            $active,
            $startDay,
            $endDay,
        );

        return $this->getPaginator($builder, $page, $perPage, false);
    }

    private function getBaseQuery(
        ?string $query = null,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        ?bool $active = null,
        ?CarbonImmutable $startDay = null,
        ?CarbonImmutable $endDay = null,
    ): QueryBuilder {
        [$orderBy, $orderDirection] = $orderBy ? Util::normalizeOrderBy('q', $orderBy, $orderDirection) : [null, null];

        $builder = $this->createQueryBuilder('q')
            ->select('q');

        if ($orderBy) {
            $builder->orderBy($orderBy, $orderDirection);
        }

        if ($query !== null) {
            $builder->groupBy('q.id');
            $this->buildQueryCondition($query, $qb, [
                'q.id',
                'q.name',
                'q.url',
            ]);
        }

        if ($active !== null) {
            $builder->andWhere('q.active = :active')
                ->setParameter('active', $active);
        }

        if ($startDay !== null) {
            $builder->andWhere('q.createdAt >= :startDay')
                ->setParameter('startDay', $startDay);
        }

        if ($endDay !== null) {
            $builder->andWhere('q.createdAt <= :endDay')
                ->setParameter('endDay', $endDay);
        }

        return $builder;
    }
}
