<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Repository;

use App\Component\Vacancy\Entity\Vacancy;
use App\Contract\Repository\FindByIdInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @method Vacancy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vacancy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vacancy[]    findAll()
 * @method Vacancy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VacancyRepository extends ServiceEntityRepository implements FindByIdInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vacancy::class);
    }

    public function findOneById(Uuid $id): ?Vacancy
    {
        return $this->find($id);
    }

    public function findOneBySourceIdAndExternalId(Uuid $sourceId, string $externalId): ?Vacancy
    {
        return $this->findOneBy(['source' => $sourceId, 'externalId' => $externalId]);
    }
}
