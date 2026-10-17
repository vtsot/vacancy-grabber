<?php

declare(strict_types=1);

namespace App\Application\Source\MessageHandler\Command;

use App\Application\Exception\Security\AccessDeniedException;
use App\Application\Security\Voter\SourceVoter;
use App\Application\Shared\Bus\CommandHandlerInterface;
use App\Application\Source\DTO\GrabSourceResultDTO;
use App\Application\Source\Message\Command\GrabSourceCommand;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Source\Grabber\GrabberProvider;
use App\Domain\Source\Repository\SourceRepository;
use App\Domain\Source\Service\VacancyImporter;
use App\Domain\Source\Service\VacancySynchronizer;
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
        private VacancySynchronizer $vacancySynchronizer,
    ) {
    }

    public function __invoke(GrabSourceCommand $command): GrabSourceResultDTO
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

        // update the vacancies changed on the source before the new ones are imported
        $synced   = $this->vacancySynchronizer->sync($source, $grabbed, $command->userId);
        $imported = $this->vacancyImporter->import($owner, $source, $grabbed, $synced->vacanciesUpdated);

        return new GrabSourceResultDTO(
            vacanciesCreated: $imported->vacanciesCreated,
            vacanciesUpdated: $synced->vacanciesUpdated,
        );
    }
}
