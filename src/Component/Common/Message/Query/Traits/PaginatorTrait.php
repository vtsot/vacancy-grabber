<?php

declare(strict_types=1);

namespace App\Component\Common\Message\Query\Traits;

use App\Component\Common\Exception\Validation\OutOfRangeException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

use function ceil;

trait PaginatorTrait
{
    /**
     * @return Paginator<mixed>
     */
    protected function getPaginator(QueryBuilder $query, int $page, int $perPage): Paginator
    {
        $paginator = new Paginator($query);

        $count   = $paginator->count();
        $maxPage = $perPage > 0 ? ceil($count / $perPage) : ($count ? 1 : 0);

        if ($maxPage < $page && $page !== 1) {
            throw new OutOfRangeException('Page is out of range');
        }

        $paginator->getQuery()
            ->setMaxResults($perPage)
            ->setFirstResult(($page - 1) * $perPage);

        return $paginator;
    }
}
