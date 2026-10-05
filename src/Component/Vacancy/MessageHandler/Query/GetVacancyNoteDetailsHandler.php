<?php

declare(strict_types=1);

namespace App\Component\Vacancy\MessageHandler\Query;

use App\Component\General\Exception\Validation\EntityNotFoundException;
use App\Component\General\Message\Query\Traits\FiltersOrderByTrait;
use App\Component\Note\Repository\NoteRepository;
use App\Component\Vacancy\DTO\VacancyNoteDTO;
use App\Component\Vacancy\DTOFactory\VacancyNoteDTOFactory;
use App\Component\Vacancy\Message\Query\GetVacancyNoteDetailsQuery;
use App\Contract\MessageHandler\QueryHandlerInterface;
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
