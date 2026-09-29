<?php

declare(strict_types=1);

namespace App\Component\Vacancy\MessageHandler\Command;

use App\Component\General\Exception\Validation\EntityNotFoundException;
use App\Component\General\Exception\Validation\ValidationFailedException;
use App\Component\Note\Entity\Note;
use App\Component\Vacancy\DTO\VacancyNoteDTO;
use App\Component\Vacancy\DTOFactory\VacancyNoteDTOFactory;
use App\Component\Vacancy\Message\Command\CreateVacancyNoteCommand;
use App\Component\Vacancy\Repository\VacancyRepository;
use App\Contract\MessageHandler\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CreateVacancyNoteHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private TranslatorInterface $translator,
        private VacancyRepository $vacancyRepository,
        private VacancyNoteDTOFactory $vacancyNoteDTOFactory,
    ) {
    }

    /**
     * @throws ValidationFailedException
     */
    public function __invoke(CreateVacancyNoteCommand $command):VacancyNoteDTO
    {
        if (!$vacancy = $this->vacancyRepository->findOneById($command->vacancyId)) {
            throw new EntityNotFoundException($this->translator->trans(id: 'VACANCY.NOT_FOUND', domain: 'validators'));
        }

        $note = new Note()
            ->setVacancy($vacancy)
            ->setNotedAt($command->notedAt)
            ->setDescription($command->description);

        $this->em->persist($note);
        $this->em->flush();

        return $this->vacancyNoteDTOFactory->fromEntity($note);
    }
}
