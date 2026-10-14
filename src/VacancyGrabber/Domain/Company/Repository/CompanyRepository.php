<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Company\Repository;

use App\Contract\Repository\BuildQueryCondition;
use App\Contract\Repository\DoctrineRepository;
use App\Contract\Repository\findAllForUserId;
use App\Contract\Repository\findOneById;
use App\Contract\Repository\findOneByIdAndUserId;
use App\Contract\Repository\Paginator;
use App\VacancyGrabber\Application\Component\Company\DTO\CompanyCriteriaDTO;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\WindowPage;

interface CompanyRepository extends DoctrineRepository, findOneById, findOneByIdAndUserId, findAllForUserId, Paginator, BuildQueryCondition
{
    public function createBaseQuery(CompanyCriteriaDTO $criteria): QueryBuilder;

    public function getAllPaginated(CompanyCriteriaDTO $criteria): WindowPage;
}
