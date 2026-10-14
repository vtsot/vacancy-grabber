<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\User\Repository;

use App\Contract\Repository\BuildQueryCondition;
use App\Contract\Repository\DoctrineRepository;
use App\Contract\Repository\findOneById;
use App\Contract\Repository\findOneByIdentifier;
use App\Contract\Repository\Paginator;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\WindowPage;

interface UserRepository extends DoctrineRepository, findOneById, findOneByIdentifier
    // , Paginator, BuildQueryCondition
{
    //    public function createBaseQuery(UserCriteriaDTO $criteria): QueryBuilder;
    //
    //    public function getAllPaginated(UserCriteriaDTO $criteria): WindowPage;
}
