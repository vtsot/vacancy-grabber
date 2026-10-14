<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Source\MessageHandler\Command;

use App\VacancyGrabber\Application\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Application\Security\Voter\SourceVoter;
use App\VacancyGrabber\Application\Shared\Bus\CommandHandlerInterface;
use App\VacancyGrabber\Application\Source\DTO\SourceDetailsDTO;
use App\VacancyGrabber\Application\Source\DTOFactory\SourceDetailsDTOFactory;
use App\VacancyGrabber\Application\Source\Message\Command\CreateSourceCommand;
use App\VacancyGrabber\Domain\Shared\ValueObject\Url;
use App\VacancyGrabber\Domain\Source\Entity\Source;
use App\VacancyGrabber\Domain\Source\Repository\SourceRepository;
use App\VacancyGrabber\Domain\User\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CreateSourceHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private SourceRepository $sourceRepository,
        private UserRepository $userRepository,
        private SourceDetailsDTOFactory $sourceDetailsDTOFactory,
    ) {
    }

    public function __invoke(CreateSourceCommand $command): SourceDetailsDTO
    {
        if (!$this->security->isGranted(SourceVoter::CREATE, [$command->userId, Source::class])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $source = Source::create(
            owner: $command->userId ? $this->userRepository->findOneById($command->userId) : null,
            name: $command->name,
            url: Url::fromNullable($command->url),
            active: (bool) $command->active,
        );

        $this->sourceRepository->save($source);

        return $this->sourceDetailsDTOFactory->fromEntity($source);
    }
}
