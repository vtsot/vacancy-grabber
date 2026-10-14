<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Note\Repository;

use App\VacancyGrabber\Domain\Note\Entity\Note;
use App\VacancyGrabber\Domain\Shared\Repository\FindOneById;
use App\VacancyGrabber\Domain\Shared\Repository\FindOneByIdAndUserId;
use App\VacancyGrabber\Domain\Shared\Repository\PaginatedResult;
use Symfony\Component\Uid\Uuid;

/**
 * @method Note|null findOneById(Uuid $id)
 * @method Note|null findOneByIdAndUserId(Uuid $id, Uuid $userId)
 */
interface NoteRepository extends FindOneById, FindOneByIdAndUserId
{
    public function save(Note $note): void;

    public function remove(Note $note): void;

    /**
     * @return PaginatedResult<Note>
     */
    public function getAllPaginated(NoteCriteria $criteria): PaginatedResult;

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
