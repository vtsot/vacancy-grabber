<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\MessageHandler\Command;

use App\Contract\MessageHandler\CommandHandlerInterface;
use App\VacancyGrabber\Application\Shared\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Application\Shared\Exception\Validation\EntityNotFoundException;
use App\VacancyGrabber\Application\Vacancy\Message\Command\UpdateVacancyNoteCommand;
use App\VacancyGrabber\Application\Vacancy\Security\Voter\VacancyNoteVoter;
use App\VacancyGrabber\Domain\Note\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class UpdateVacancyNoteHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private EntityManagerInterface $em,
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

        $note
            ->setNotedAt($command->notedAt)
            ->setDescription($command->description);

        $this->em->flush();
    }
}
