<?php

declare(strict_types=1);

namespace App\Application\Vacancy\MessageHandler\Query;

use App\Application\Shared\Bus\QueryHandlerInterface;
use App\Application\Shared\Message\Query\Traits\FiltersOrderByTrait;
use App\Application\Vacancy\DTO\VacancyNoteDTO;
use App\Application\Vacancy\DTOFactory\VacancyNoteDTOFactory;
use App\Application\Vacancy\Message\Query\GetVacancyNoteDetailsQuery;
use App\Domain\Note\Repository\NoteRepository;
use App\Domain\Shared\Exception\EntityNotFoundException;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class GetVacancyNoteDetailsHandler implements QueryHandlerInterface
{
    use FiltersOrderByTrait;

    public function __construct(
        private TranslatorInterface $translator,
        private NoteRepository $noteRepository,
        private VacancyNoteDTOFactory $vacancyNoteDTOFactory,
    ) {
    }

    public function __invoke(GetVacancyNoteDetailsQuery $query): VacancyNoteDTO
    {
        if (!($note = $this->noteRepository->findOneByUserIdAndNoteIdAndVacancyId($query->userId, $query->noteId, $query->vacancyId))) {
            throw new EntityNotFoundException($this->translator->trans(id: 'NOTE.NOT_FOUND', domain: 'validators'));
        }

        return $this->vacancyNoteDTOFactory->fromEntity($note);
    }
}
