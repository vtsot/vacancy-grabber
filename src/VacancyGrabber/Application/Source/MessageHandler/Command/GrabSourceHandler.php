<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Source\MessageHandler\Command;

use App\VacancyGrabber\Application\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Application\Security\Voter\SourceVoter;
use App\VacancyGrabber\Application\Shared\Bus\CommandHandlerInterface;
use App\VacancyGrabber\Application\Source\Message\Command\GrabSourceCommand;
use App\VacancyGrabber\Domain\Shared\Exception\EntityNotFoundException;
use App\VacancyGrabber\Domain\Source\Grabber\GrabberProvider;
use App\VacancyGrabber\Domain\Source\Repository\SourceRepository;
use App\VacancyGrabber\Domain\Source\Service\VacancyImporter;
use App\VacancyGrabber\Domain\User\Repository\UserRepository;
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
