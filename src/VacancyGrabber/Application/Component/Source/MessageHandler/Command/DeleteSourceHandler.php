<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Source\MessageHandler\Command;

use App\Contract\MessageHandler\CommandHandlerInterface;
use App\VacancyGrabber\Application\Component\Source\Message\Command\DeleteSourceCommand;
use App\VacancyGrabber\Application\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Application\Exception\Validation\EntityNotFoundException;
use App\VacancyGrabber\Application\Security\Voter\SourceVoter;
use App\VacancyGrabber\Domain\Source\Repository\SourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DeleteSourceHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private EntityManagerInterface $em,
        private SourceRepository $sourceRepository,
    ) {
    }

    public function __invoke(DeleteSourceCommand $command): void
    {
        if (!$source = $this->sourceRepository->findOneById($command->sourceId)) {
            throw new EntityNotFoundException($this->translator->trans(id: 'SOURCE.NOT_FOUND', domain: 'validators'));
        }

        if (!$this->security->isGranted(SourceVoter::DELETE, [$command->userId, $source])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $this->em->remove($source);
        $this->em->flush();
    }
}
