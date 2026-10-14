<?php

declare(strict_types=1);

namespace App\Application\Vacancy\MessageHandler\Command;

use App\Application\Exception\Security\AccessDeniedException;
use App\Application\Security\Voter\VacancyVoter;
use App\Application\Shared\Bus\CommandHandlerInterface;
use App\Application\Vacancy\Message\Command\DeleteVacancyCommand;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Vacancy\Repository\VacancyRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DeleteVacancyHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private VacancyRepository $vacancyRepository,
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

        $this->vacancyRepository->remove($vacancy);
    }
}
