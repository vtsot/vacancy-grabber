<?php

declare(strict_types=1);

namespace App\Component\Source\MessageHandler\Command;

use App\Component\General\Exception\Validation\EntityNotFoundException;
use App\Component\Source\Message\Command\DeleteSourceCommand;
use App\Component\Source\Repository\SourceRepository;
use App\Contract\MessageHandler\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class DeleteSourceHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private TranslatorInterface $translator,
        private SourceRepository $sourceRepository,
    ) {
    }

    public function __invoke(DeleteSourceCommand $command): void
    {
        if (!$source = $this->sourceRepository->findOneById($command->id)) {
            throw new EntityNotFoundException($this->translator->trans('SOURCE.NOT_FOUND'));
        }

        $this->em->remove($source);
        $this->em->flush();
    }
}
