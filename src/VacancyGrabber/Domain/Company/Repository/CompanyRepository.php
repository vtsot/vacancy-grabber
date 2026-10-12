<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Company\Repository;

use App\Contract\Repository\findOneById;
use App\Contract\Repository\findOneByIdAndUserId;
use App\VacancyGrabber\Domain\Company\DTO\CompanyCriteriaDTO;
use App\VacancyGrabber\Domain\Company\Entity\Company;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\WindowPage;
use Symfony\Component\Uid\Uuid;

interface CompanyRepository extends findOneById, findOneByIdAndUserId
{
    public function findOneById(Uuid $id): ?Company;

    public function findOneByIdAndUserId(Uuid $id, Uuid $userId): ?Company;

    public function findOneByName(string $name): ?Company;

    /**
     * @return array<int, Company>
     */
    public function findAllForUserId(?Uuid $userId = null, array $companyIds = []): array;

    //        ?Uuid $userId = null,
    //        ?string $query = null,
    //        ?string $orderBy = null,
    //        ?string $orderDirection = null,
    //        ?string $name = null,
    public function createBaseQuery(CompanyCriteriaDTO $criteria): QueryBuilder;

    //        ?Uuid $userId,
    //        ?int $page,
    //        ?int $perPage,
    //        ?string $query = null,
    //        ?string $orderBy = null,
    //        ?string $orderDirection = null,
    public function getAllPaginated(CompanyCriteriaDTO $criteria): WindowPage;
}
