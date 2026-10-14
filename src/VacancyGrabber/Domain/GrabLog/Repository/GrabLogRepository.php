<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\GrabLog\Repository;

use App\VacancyGrabber\Domain\GrabLog\Entity\GrabLog;
use App\VacancyGrabber\Domain\Shared\Repository\FindOneById;
use App\VacancyGrabber\Domain\Shared\Repository\FindOneByIdAndUserId;
use App\VacancyGrabber\Domain\Shared\Repository\PaginatedResult;
use Symfony\Component\Uid\Uuid;

/**
 * @method GrabLog|null findOneById(Uuid $id)
 * @method GrabLog|null findOneByIdAndUserId(Uuid $id, Uuid $userId)
 */
interface GrabLogRepository extends FindOneById, FindOneByIdAndUserId
{
    public function save(GrabLog $grabLog): void;

    public function remove(GrabLog $grabLog): void;

    /**
     * @return PaginatedResult<GrabLog>
     */
    public function getAllPaginated(GrabLogCriteria $criteria): PaginatedResult;
}
