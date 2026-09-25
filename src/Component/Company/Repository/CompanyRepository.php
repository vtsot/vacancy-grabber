<?php

declare(strict_types=1);

namespace App\Component\Company\Repository;

use App\Component\Company\Entity\Company;
use App\Contract\Repository\FindByIdInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @method Company|null find($id, $lockMode = null, $lockVersion = null)
 * @method Company|null findOneBy(array $criteria, array $orderBy = null)
 * @method Company[]    findAll()
 * @method Company[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyRepository extends ServiceEntityRepository implements FindByIdInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    public function findOneById(Uuid $id): ?Company
    {
        return $this->find($id);
    }

    public function findOneByName(string $name): ?Company
    {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * @return array<int, Company>
     */
    public function findAllSortByName(): array
    {
        return $this->findBy([], ['name' => 'asc'], 2);
    }
}
