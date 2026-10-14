<?php

declare(strict_types=1);

namespace App\Contract\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\WindowPage;

interface Paginator
{
    public const int MAX_PER_PAGE = 1000;

    public function getPaginator(
        QueryBuilder $builder,
        int $page,
        int $perPage,
        bool $fetchJoinCollection = true,
    ): WindowPage;
}
