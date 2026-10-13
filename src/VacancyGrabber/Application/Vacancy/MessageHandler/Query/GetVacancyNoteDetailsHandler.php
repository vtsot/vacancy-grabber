<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\MessageHandler\Query;

use App\Component\General\Exception\Validation\EntityNotFoundException;
use App\Component\General\Message\Query\Traits\FiltersOrderByTrait;
use App\Contract\MessageHandler\QueryHandlerInterface;
use App\VacancyGrabber\Application\Vacancy\DTO\VacancyNoteDTO;
use App\VacancyGrabber\Application\Vacancy\DTOFactory\VacancyNoteDTOFactory;
use App\VacancyGrabber\Application\Vacancy\Message\Query\GetVacancyNoteDetailsQuery;
use App\VacancyGrabber\Domain\Note\Repository\NoteRepositoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class GetVacancyNoteDetailsHandler implements QueryHandlerInterface
{
    use FiltersOrderByTrait;

    public function __construct(
        private TranslatorInterface $translator,
        private NoteRepositoryInterface $noteRepository,
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
