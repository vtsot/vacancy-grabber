<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\GrabLog\Repository;

use App\Contract\Repository\BuildQueryCondition;
use App\Contract\Repository\findOneById;
use App\Contract\Repository\findOneByIdAndUserId;
use App\Contract\Repository\Paginator;
use App\VacancyGrabber\Application\Component\GrabLog\DTO\GrabLogCriteriaDTO;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\WindowPage;

interface GrabLogRepository extends findOneById, findOneByIdAndUserId, Paginator, BuildQueryCondition
{
    public function createBaseQuery(GrabLogCriteriaDTO $criteria): QueryBuilder;

    public function getAllPaginated(GrabLogCriteriaDTO $criteria): WindowPage;
}
