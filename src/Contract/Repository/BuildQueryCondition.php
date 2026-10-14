<?php

declare(strict_types=1);

namespace App\Contract\Repository;

use Doctrine\ORM\QueryBuilder;

interface BuildQueryCondition
{
    public function buildQueryCondition(
        string $query,
        QueryBuilder $builder,
        array $fields,
    ): QueryBuilder;
}
