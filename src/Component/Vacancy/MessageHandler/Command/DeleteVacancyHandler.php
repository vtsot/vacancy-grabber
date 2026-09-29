<?php

declare(strict_types=1);

namespace App\Component\Vacancy\MessageHandler\Command;

use App\Component\General\Exception\Validation\EntityNotFoundException;
use App\Component\Vacancy\Message\Command\DeleteVacancyCommand;
use App\Component\Vacancy\Repository\VacancyRepository;
use App\Contract\MessageHandler\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DeleteVacancyHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private TranslatorInterface $translator,
        private VacancyRepository $vacancyRepository,
    ) {
    }

    public function __invoke(DeleteVacancyCommand $command): void
    {
        if (!$vacancy = $this->vacancyRepository->findOneById($command->id)) {
            throw new EntityNotFoundException($this->translator->trans(id: 'VACANCY.NOT_FOUND', domain: 'validators'));
        }

        $this->em->remove($vacancy);
        $this->em->flush();
    }
}
