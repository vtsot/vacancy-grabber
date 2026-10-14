<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Note\Repository;

use App\Contract\Repository\BuildQueryCondition;
use App\Contract\Repository\findOneById;
use App\Contract\Repository\findOneByIdAndUserId;
use App\Contract\Repository\Paginator;
use App\VacancyGrabber\Application\Component\Note\DTO\NoteCriteriaDTO;
use App\VacancyGrabber\Domain\Note\Entity\Note;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\WindowPage;
use Symfony\Component\Uid\Uuid;

interface NoteRepository extends findOneById, findOneByIdAndUserId, Paginator, BuildQueryCondition
{
    public function createBaseQuery(NoteCriteriaDTO $criteria): QueryBuilder;

    public function getAllPaginated(NoteCriteriaDTO $criteria): WindowPage;

    public function findOneByUserIdAndNoteIdAndVacancyId(Uuid $userId, Uuid $noteId, Uuid $vacancyId): ?Note;

    /**
     * @param Uuid[] $vacancyIds
     *
     * @return array<string, array{total: int}>
     */
    public function countByVacancyIds(?Uuid $userId, array $vacancyIds): array;

    /**
     * @return Uuid[]
     */
    public function findAllCompanyIdsByUserId(Uuid $userId): array;
}
