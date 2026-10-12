<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Infrastructure\Repository;

use App\VacancyGrabber\Domain\Company\DTO\CompanyCriteriaDTO;
use App\VacancyGrabber\Domain\Company\Entity\Company;
use App\VacancyGrabber\Domain\Company\Repository\CompanyRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Window;
use Doctrine\ORM\Tools\Pagination\WindowPage;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class DoctrineCompanyRepository extends ServiceEntityRepository implements CompanyRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function findOneById(Uuid $id): ?Company
    {
        return null;
    }

    public function findOneByIdAndUserId(Uuid $id, Uuid $userId): ?Company
    {
        return null;
    }

    public function findOneByName(string $name): ?Company
    {
        return null;
    }

    public function findAllForUserId(?Uuid $userId = null, array $companyIds = []): array
    {
        return [];
    }

    //        ?Uuid $userId = null,
    //        ?string $query = null,
    //        ?string $orderBy = null,
    //        ?string $orderDirection = null,
    //        ?string $name = null,
    public function createBaseQuery(CompanyCriteriaDTO $criteria): QueryBuilder
    {
        return $this->createQueryBuilder('q');
    }

    //        ?Uuid $userId,
    //        ?int $page,
    //        ?int $perPage,
    //        ?string $query = null,
    //        ?string $orderBy = null,
    //        ?string $orderDirection = null,
    public function getAllPaginated(CompanyCriteriaDTO $criteria): WindowPage
    {
        return new WindowPage([], 0, new Window(0, 0));
    }
}
