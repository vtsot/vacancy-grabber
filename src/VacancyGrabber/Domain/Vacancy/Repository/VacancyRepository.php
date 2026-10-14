<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Vacancy\Repository;

use App\Contract\Repository\BuildQueryCondition;
use App\Contract\Repository\findOneById;
use App\Contract\Repository\findOneByIdAndUserId;
use App\Contract\Repository\Paginator;
use App\VacancyGrabber\Application\Component\Vacancy\DTO\VacancyCriteriaDTO;
use App\VacancyGrabber\Domain\Vacancy\Entity\Vacancy;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\WindowPage;
use Symfony\Component\Uid\Uuid;

interface VacancyRepository extends findOneById, findOneByIdAndUserId, Paginator, BuildQueryCondition
{
    public function createBaseQuery(VacancyCriteriaDTO $criteria): QueryBuilder;

    public function getAllPaginated(VacancyCriteriaDTO $criteria): WindowPage;

    public function findOneByUserIdAndSourceIdAndExternalId(Uuid $userId, Uuid $sourceId, string $externalId): ?Vacancy;

    /**
     * @return array<string, array{total: int}>
     */
    public function countByStatus(?Uuid $userId): array;

    /**
     * @return array<string, array{total: int}>
     */
    public function countByCreatedAt(?Uuid $userId = null): array;

    /**
     * @return array<string, array{total: int}>
     */
    public function countByResumeSentAt(?Uuid $userId = null): array;

    /**
     * @return array<string, array{total: int}>
     */
    public function countByDeadLineAt(?Uuid $userId = null): array;

    /**
     * @return Uuid[]
     */
    public function findAllCompanyIdsByUserId(Uuid $userId): array;
}
