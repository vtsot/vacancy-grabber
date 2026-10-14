<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\MessageHandler\Command;

use App\VacancyGrabber\Application\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Application\Exception\Validation\ValidationFailedException;
use App\VacancyGrabber\Application\Security\Voter\VacancyNoteVoter;
use App\VacancyGrabber\Application\Shared\Bus\CommandHandlerInterface;
use App\VacancyGrabber\Application\Vacancy\DTO\VacancyNoteDTO;
use App\VacancyGrabber\Application\Vacancy\DTOFactory\VacancyNoteDTOFactory;
use App\VacancyGrabber\Application\Vacancy\Message\Command\CreateVacancyNoteCommand;
use App\VacancyGrabber\Domain\Note\Entity\Note;
use App\VacancyGrabber\Domain\Note\Repository\NoteRepository;
use App\VacancyGrabber\Domain\Shared\Exception\EntityNotFoundException;
use App\VacancyGrabber\Domain\User\Repository\UserRepository;
use App\VacancyGrabber\Domain\Vacancy\Repository\VacancyRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CreateVacancyNoteHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private NoteRepository $noteRepository,
        private UserRepository $userRepository,
        private VacancyRepository $vacancyRepository,
        private VacancyNoteDTOFactory $vacancyNoteDTOFactory,
    ) {
    }

    /**
     * @throws ValidationFailedException
     */
    public function __invoke(CreateVacancyNoteCommand $command): VacancyNoteDTO
    {
        if (!$vacancy = $this->vacancyRepository->findOneById($command->vacancyId)) {
            throw new EntityNotFoundException($this->translator->trans(id: 'VACANCY.NOT_FOUND', domain: 'validators'));
        }

        if (!$this->security->isGranted(VacancyNoteVoter::CREATE, [$command->userId, Note::class])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $note = $vacancy->writeNote(
            author: $command->userId ? $this->userRepository->findOneById($command->userId) : null,
            description: $command->description,
            notedAt: $command->notedAt,
        );

        $this->noteRepository->save($note);

        return $this->vacancyNoteDTOFactory->fromEntity($note);
    }
}
