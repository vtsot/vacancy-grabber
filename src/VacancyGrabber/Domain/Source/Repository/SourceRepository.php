<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Source\Repository;

use App\Contract\Repository\BuildQueryCondition;
use App\Contract\Repository\findOneById;
use App\Contract\Repository\findOneByIdAndUserId;
use App\Contract\Repository\Paginator;
use App\VacancyGrabber\Application\Source\DTO\SourceCriteriaDTO;
use App\VacancyGrabber\Domain\Source\Entity\Source;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\WindowPage;
use Symfony\Component\Uid\Uuid;

interface SourceRepository extends findOneById, findOneByIdAndUserId, Paginator, BuildQueryCondition
{
    public function createBaseQuery(SourceCriteriaDTO $criteria): QueryBuilder;

    public function getAllPaginated(SourceCriteriaDTO $criteria): WindowPage;

    /**
     * @return array<int, Source>
     */
    public function findAllSortByName(?Uuid $userId = null): array;
}
