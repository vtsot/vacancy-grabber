<?php

declare(strict_types=1);

namespace App\Application\Vacancy\MessageHandler\Command;

use App\Application\Exception\Security\AccessDeniedException;
use App\Application\Security\Voter\VacancyNoteVoter;
use App\Application\Shared\Bus\CommandHandlerInterface;
use App\Application\Vacancy\Message\Command\UpdateVacancyNoteCommand;
use App\Domain\Note\Repository\NoteRepository;
use App\Domain\Shared\Exception\EntityNotFoundException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class UpdateVacancyNoteHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private NoteRepository $noteRepository,
    ) {
    }

    public function __invoke(UpdateVacancyNoteCommand $command): void
    {
        if (!($note = $this->noteRepository->findOneByUserIdAndNoteIdAndVacancyId($command->userId, $command->noteId, $command->vacancyId))) {
            throw new EntityNotFoundException($this->translator->trans(id: 'NOTE.NOT_FOUND', domain: 'validators'));
        }

        if (!$this->security->isGranted(VacancyNoteVoter::UPDATE, [$command->userId, $note])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $note->changeNotedAt($command->notedAt);
        $note->rewrite($command->description);

        $this->noteRepository->save($note);
    }
}
