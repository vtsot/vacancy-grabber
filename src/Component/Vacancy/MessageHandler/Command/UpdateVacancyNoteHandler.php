<?php

declare(strict_types=1);

namespace App\Component\Vacancy\MessageHandler\Command;

use App\Component\General\Exception\Validation\EntityNotFoundException;
use App\Component\Note\Repository\NoteRepository;
use App\Component\Vacancy\Message\Command\UpdateVacancyNoteCommand;
use App\Contract\MessageHandler\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class UpdateVacancyNoteHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private TranslatorInterface $translator,
        private NoteRepository $noteRepository,
    ) {
    }

    public function __invoke(UpdateVacancyNoteCommand $command): void
    {
        if (!($note = $this->noteRepository->findOneByIdAndVacancyId($command->noteId, $command->vacancyId))) {
            throw new EntityNotFoundException($this->translator->trans(id: 'NOTE.NOT_FOUND', domain: 'validators'));
        }

        $note
            ->setNotedAt($command->notedAt)
            ->setDescription($command->description);

        $this->em->flush();
    }
}
