<?php

declare(strict_types=1);

namespace App\Component\General\Shared\RepositoryTrait;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\OffsetPaginator;
use Doctrine\ORM\Tools\Pagination\Window;
use Doctrine\ORM\Tools\Pagination\WindowPage;

trait PaginatorRepositoryTrait
{
    private const int MAX_PER_PAGE = 1000;

    protected function getPaginator(QueryBuilder $builder, int $page, int $perPage, bool $fetchJoinCollection = true): WindowPage
    {
        $page    = $page > 0 ? $page : 1;
        $perPage = $perPage > 0 && $perPage <= static::MAX_PER_PAGE ? $perPage : static::MAX_PER_PAGE;

        $query = $builder
            ->setMaxResults($perPage)
            ->setFirstResult(($page - 1) * $perPage)
            ->getQuery();

        $window = Window::fromPageNumberAndSize($page, $perPage);

        return new OffsetPaginator($fetchJoinCollection)->paginate($query, $window);
    }
}
