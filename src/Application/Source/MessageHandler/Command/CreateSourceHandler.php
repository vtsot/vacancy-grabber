<?php

declare(strict_types=1);

namespace App\Application\Source\MessageHandler\Command;

use App\Application\Exception\Security\AccessDeniedException;
use App\Application\Security\Voter\SourceVoter;
use App\Application\Shared\Bus\CommandHandlerInterface;
use App\Application\Source\DTO\SourceDetailsDTO;
use App\Application\Source\DTOFactory\SourceDetailsDTOFactory;
use App\Application\Source\Message\Command\CreateSourceCommand;
use App\Domain\Shared\ValueObject\Url;
use App\Domain\Source\Entity\Source;
use App\Domain\Source\Repository\SourceRepository;
use App\Domain\User\Repository\UserRepository;
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
