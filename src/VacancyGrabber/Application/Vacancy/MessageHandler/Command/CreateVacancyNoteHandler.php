<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\MessageHandler\Command;

use App\Component\General\Exception\Security\AccessDeniedException;
use App\Component\General\Exception\Validation\EntityNotFoundException;
use App\Component\General\Exception\Validation\ValidationFailedException;
use App\Contract\MessageHandler\CommandHandlerInterface;
use App\VacancyGrabber\Application\Vacancy\DTO\VacancyNoteDTO;
use App\VacancyGrabber\Application\Vacancy\DTOFactory\VacancyNoteDTOFactory;
use App\VacancyGrabber\Application\Vacancy\Message\Command\CreateVacancyNoteCommand;
use App\VacancyGrabber\Application\Vacancy\Security\Voter\VacancyNoteVoter;
use App\VacancyGrabber\Domain\Note\Entity\Note;
use App\VacancyGrabber\Domain\User\Entity\User;
use App\VacancyGrabber\Domain\Vacancy\Repository\VacancyRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CreateVacancyNoteHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private EntityManagerInterface $em,
        private VacancyRepositoryInterface $vacancyRepository,
        private VacancyNoteDTOFactory $vacancyNoteDTOFactory,
    ) {
    }

    /**
     * @throws ValidationFailedException
     * @throws ORMException
     */
    public function __invoke(CreateVacancyNoteCommand $command): VacancyNoteDTO
    {
        if (!$vacancy = $this->vacancyRepository->findOneById($command->vacancyId)) {
            throw new EntityNotFoundException($this->translator->trans(id: 'VACANCY.NOT_FOUND', domain: 'validators'));
        }

        if (!$this->security->isGranted(VacancyNoteVoter::CREATE, [$command->userId, Note::class])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $note = new Note()
            ->setUser($command->userId ? $this->em->getReference(User::class, $command->userId) : null)
            ->setVacancy($vacancy)
            ->setNotedAt($command->notedAt)
            ->setDescription($command->description);

        $this->em->persist($note);
        $this->em->flush();

        return $this->vacancyNoteDTOFactory->fromEntity($note);
    }
}
