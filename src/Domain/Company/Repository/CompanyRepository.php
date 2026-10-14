<?php

declare(strict_types=1);

namespace App\Domain\Company\Repository;

use App\Domain\Company\Entity\Company;
use App\Domain\Shared\Repository\FindAllForUserId;
use App\Domain\Shared\Repository\FindOneById;
use App\Domain\Shared\Repository\FindOneByIdAndUserId;
use App\Domain\Shared\Repository\PaginatedResult;
use Symfony\Component\Uid\Uuid;

/**
 * @method Company|null findOneById(Uuid $id)
 * @method Company|null findOneByIdAndUserId(Uuid $id, Uuid $userId)
 */
interface CompanyRepository extends FindOneById, FindOneByIdAndUserId, FindAllForUserId
{
    public function save(Company $company): void;

    public function remove(Company $company): void;

    /**
     * @return PaginatedResult<Company>
     */
    public function getAllPaginated(CompanyCriteria $criteria): PaginatedResult;

    public function findOneByName(string $name): ?Company;
}
