<?php

declare(strict_types=1);

namespace App\Application\Vacancy\MessageHandler\Command;

use App\Application\Exception\Security\AccessDeniedException;
use App\Application\Exception\Validation\ValidationFailedException;
use App\Application\Security\Voter\VacancyNoteVoter;
use App\Application\Shared\Bus\CommandHandlerInterface;
use App\Application\Vacancy\DTO\VacancyNoteDTO;
use App\Application\Vacancy\DTOFactory\VacancyNoteDTOFactory;
use App\Application\Vacancy\Message\Command\CreateVacancyNoteCommand;
use App\Domain\Note\Entity\Note;
use App\Domain\Note\Repository\NoteRepository;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\User\Repository\UserRepository;
use App\Domain\Vacancy\Repository\VacancyRepository;
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
