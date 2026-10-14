<?php

declare(strict_types=1);

namespace App\Domain\GrabLog\Repository;

use App\Domain\GrabLog\Entity\GrabLog;
use App\Domain\Shared\Repository\FindOneById;
use App\Domain\Shared\Repository\FindOneByIdAndUserId;
use App\Domain\Shared\Repository\PaginatedResult;
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
