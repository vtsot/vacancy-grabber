<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Shared\RepositoryTrait\Doctrine;

use App\Contract\Repository\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\OffsetPaginator;
use Doctrine\ORM\Tools\Pagination\Window;
use Doctrine\ORM\Tools\Pagination\WindowPage;

trait PaginatorRepositoryTrait
{
    public function getPaginator(
        QueryBuilder $qb,
        int $page,
        int $perPage,
        bool $fetchJoinCollection = true,
    ): WindowPage {
        $page    = $page > 0 ? $page : 1;
        $perPage = $perPage > 0 && $perPage <= Paginator::MAX_PER_PAGE ? $perPage : Paginator::MAX_PER_PAGE;

        $query = $qb
            ->setMaxResults($perPage)
            ->setFirstResult(($page - 1) * $perPage)
            ->getQuery();

        $window = Window::fromPageNumberAndSize($page, $perPage);

        return new OffsetPaginator($fetchJoinCollection)->paginate($query, $window);
    }
}
