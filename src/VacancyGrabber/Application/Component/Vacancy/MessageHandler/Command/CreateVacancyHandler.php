<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Vacancy\MessageHandler\Command;

use App\Contract\MessageHandler\CommandHandlerInterface;
use App\VacancyGrabber\Application\Component\Vacancy\DTO\VacancyDetailsDTO;
use App\VacancyGrabber\Application\Component\Vacancy\DTOFactory\VacancyDetailsDTOFactory;
use App\VacancyGrabber\Application\Component\Vacancy\EntityFactory\VacancyEntityFactory;
use App\VacancyGrabber\Application\Component\Vacancy\Message\Command\CreateVacancyCommand;
use App\VacancyGrabber\Application\Component\Vacancy\Security\Voter\VacancyVoter;
use App\VacancyGrabber\Application\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Application\Exception\Validation\ValidationFailedException;
use App\VacancyGrabber\Domain\User\Entity\User;
use App\VacancyGrabber\Domain\Vacancy\Entity\Vacancy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CreateVacancyHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private EntityManagerInterface $em,
        private VacancyEntityFactory $vacancyEntityFactory,
        private VacancyDetailsDTOFactory $vacancyDetailsDTOFactory,
    ) {
    }

    /**
     * @throws ORMException
     * @throws ValidationFailedException
     */
    public function __invoke(CreateVacancyCommand $command): VacancyDetailsDTO
    {
        if (!$this->security->isGranted(VacancyVoter::CREATE, [$command->userId, Vacancy::class])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $vacancy = $this->vacancyEntityFactory->processCommand($command);
        $vacancy->setUser($command->userId ? $this->em->getReference(User::class, $command->userId) : null);

        $this->em->persist($vacancy);
        $this->em->flush();

        return $this->vacancyDetailsDTOFactory->fromEntity($vacancy);
    }
}
