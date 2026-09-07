<?php

declare(strict_types=1);

namespace App\Repository;

use App\Contract\Repository\FindByIdInterface;
use App\Entity\GrabLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @method GrabLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method GrabLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method GrabLog[]    findAll()
 * @method GrabLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GrabLogRepository extends ServiceEntityRepository implements FindByIdInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GrabLog::class);
    }

    public function findOneById(Uuid $id): ?GrabLog
    {
        return $this->find($id);
    }
}
