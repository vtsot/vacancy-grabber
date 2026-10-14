<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\MessageHandler\Query;

use App\VacancyGrabber\Application\Shared\Bus\QueryHandlerInterface;
use App\VacancyGrabber\Application\Shared\Message\Query\Traits\FiltersOrderByTrait;
use App\VacancyGrabber\Application\Vacancy\DTO\VacancyNoteDTO;
use App\VacancyGrabber\Application\Vacancy\DTOFactory\VacancyNoteDTOFactory;
use App\VacancyGrabber\Application\Vacancy\Message\Query\GetVacancyNoteDetailsQuery;
use App\VacancyGrabber\Domain\Note\Repository\NoteRepository;
use App\VacancyGrabber\Domain\Shared\Exception\EntityNotFoundException;
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
