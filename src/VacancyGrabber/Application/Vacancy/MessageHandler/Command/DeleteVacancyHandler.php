<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\MessageHandler\Command;

use App\Contract\MessageHandler\CommandHandlerInterface;
use App\VacancyGrabber\Application\Shared\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Application\Shared\Exception\Validation\EntityNotFoundException;
use App\VacancyGrabber\Application\Vacancy\Message\Command\DeleteVacancyCommand;
use App\VacancyGrabber\Application\Vacancy\Security\Voter\VacancyVoter;
use App\VacancyGrabber\Domain\Vacancy\Repository\VacancyRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DeleteVacancyHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private EntityManagerInterface $em,
        private VacancyRepositoryInterface $vacancyRepository,
    ) {
    }

    public function __invoke(DeleteVacancyCommand $command): void
    {
        if (!$vacancy = $this->vacancyRepository->findOneById($command->vacancyId)) {
            throw new EntityNotFoundException($this->translator->trans(id: 'VACANCY.NOT_FOUND', domain: 'validators'));
        }

        if (!$this->security->isGranted(VacancyVoter::DELETE, [$command->userId, $vacancy])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $this->em->remove($vacancy);
        $this->em->flush();
    }
}
