<?php

declare(strict_types=1);

namespace App\Domain\Vacancy\Repository;

use App\Domain\Shared\Repository\FindOneById;
use App\Domain\Shared\Repository\FindOneByIdAndUserId;
use App\Domain\Shared\Repository\PaginatedResult;
use App\Domain\Vacancy\Entity\Vacancy;
use Symfony\Component\Uid\Uuid;

/**
 * @method Vacancy|null findOneById(Uuid $id)
 * @method Vacancy|null findOneByIdAndUserId(Uuid $id, Uuid $userId)
 */
interface VacancyRepository extends FindOneById, FindOneByIdAndUserId
{
    public function save(Vacancy $vacancy): void;

    public function remove(Vacancy $vacancy): void;

    /**
     * @return PaginatedResult<Vacancy>
     */
    public function getAllPaginated(VacancyCriteria $criteria): PaginatedResult;

    public function findOneByUserIdAndSourceIdAndExternalId(?Uuid $userId, Uuid $sourceId, string $externalId): ?Vacancy;

    /**
     * @param Uuid|null $userId null means vacancies of every owner
     *
     * @return list<Vacancy>
     */
    public function findAllBySourceId(Uuid $sourceId, ?Uuid $userId = null): array;

    public function findFirstCreatedByUserId(?Uuid $userId): ?Vacancy;

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
