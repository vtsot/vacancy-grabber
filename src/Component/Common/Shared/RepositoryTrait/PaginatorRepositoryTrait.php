<?php

declare(strict_types=1);

namespace App\Component\Common\Shared\RepositoryTrait;

use App\Component\Common\Exception\Validation\OutOfRangeException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

use function ceil;

trait PaginatorRepositoryTrait
{
    private const int MAXIMUM_PER_PAGE = 1000;

    /**
     * @param bool $fetchJoinCollection whether the query joins a collection (true by default)
     *
     * @return Paginator<mixed>
     */
    protected function getPaginator(QueryBuilder $query, int $page, int $perPage, bool $fetchJoinCollection = true): Paginator
    {
        $paginator = new Paginator($query, $fetchJoinCollection);

        $page    = $page > 0 ? $page : 1;
        $perPage = $perPage > 0 && $perPage <= static::MAXIMUM_PER_PAGE ? $perPage : static::MAXIMUM_PER_PAGE;

        $count   = $paginator->count();
        $maxPage = ceil($count / $perPage);

        if ($maxPage < $page && $page !== 1) {
            throw new OutOfRangeException('Page is out of range');
        }

        $paginator->getQuery()
            ->setMaxResults($perPage)
            ->setFirstResult(($page - 1) * $perPage);

        return $paginator;
    }
}
