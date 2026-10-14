<?php

declare(strict_types=1);

namespace App\Application\Source\MessageHandler\Command;

use App\Application\Exception\Security\AccessDeniedException;
use App\Application\Security\Voter\SourceVoter;
use App\Application\Shared\Bus\CommandHandlerInterface;
use App\Application\Source\Message\Command\GrabSourceCommand;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Source\Grabber\GrabberProvider;
use App\Domain\Source\Repository\SourceRepository;
use App\Domain\Source\Service\VacancyImporter;
use App\Domain\User\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class GrabSourceHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private SourceRepository $sourceRepository,
        private UserRepository $userRepository,
        private GrabberProvider $grabberProvider,
        private VacancyImporter $vacancyImporter,
    ) {
    }

    /**
     * @return int number of vacancies created
     */
    public function __invoke(GrabSourceCommand $command): int
    {
        if (!($source = $this->sourceRepository->findOneById($command->sourceId))) {
            throw new EntityNotFoundException(
                $this->translator->trans(id: 'SOURCE.NOT_FOUND', domain: 'validators')
            );
        }

        if (!$this->security->isGranted(SourceVoter::GRAB, [$command->userId, $source])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $grabbed = $this->grabberProvider->forSource($source)->grab((string) $source->getUrl());
        $owner   = $command->userId ? $this->userRepository->findOneById($command->userId) : null;

        return $this->vacancyImporter->import($owner, $source, $grabbed)->vacanciesCreated;
    }
}
