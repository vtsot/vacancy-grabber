<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Note\Repository;

use App\Contract\Repository\BuildQueryCondition;
use App\Contract\Repository\findOneById;
use App\Contract\Repository\findOneByIdAndUserId;
use App\Contract\Repository\Paginator;
use App\VacancyGrabber\Application\Note\DTO\NoteCriteriaDTO;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\WindowPage;

interface NoteRepositoryInterface extends findOneById, findOneByIdAndUserId, Paginator, BuildQueryCondition
{
    public function createBaseQuery(NoteCriteriaDTO $criteria): QueryBuilder;

    public function getAllPaginated(NoteCriteriaDTO $criteria): WindowPage;
}
