<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Source\Repository;

use App\VacancyGrabber\Domain\Shared\Repository\FindOneById;
use App\VacancyGrabber\Domain\Shared\Repository\FindOneByIdAndUserId;
use App\VacancyGrabber\Domain\Shared\Repository\PaginatedResult;
use App\VacancyGrabber\Domain\Source\Entity\Source;
use Symfony\Component\Uid\Uuid;

/**
 * @method Source|null findOneById(Uuid $id)
 * @method Source|null findOneByIdAndUserId(Uuid $id, Uuid $userId)
 */
interface SourceRepository extends FindOneById, FindOneByIdAndUserId
{
    public function save(Source $source): void;

    public function remove(Source $source): void;

    /**
     * @return PaginatedResult<Source>
     */
    public function getAllPaginated(SourceCriteria $criteria): PaginatedResult;

    /**
     * @return array<int, Source>
     */
    public function findAllSortByName(?Uuid $userId = null): array;
}
