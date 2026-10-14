<?php

declare(strict_types=1);

namespace App\Application\Source\MessageHandler\Command;

use App\Application\Exception\Security\AccessDeniedException;
use App\Application\Security\Voter\SourceVoter;
use App\Application\Shared\Bus\CommandHandlerInterface;
use App\Application\Source\Message\Command\DeleteSourceCommand;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Source\Repository\SourceRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DeleteSourceHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
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

        $this->sourceRepository->remove($source);
    }
}
