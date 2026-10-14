<?php

declare(strict_types=1);

namespace App\Application\Source\MessageHandler\Command;

use App\Application\Exception\Security\AccessDeniedException;
use App\Application\Security\Voter\SourceVoter;
use App\Application\Shared\Bus\CommandHandlerInterface;
use App\Application\Source\DTO\SourceDetailsDTO;
use App\Application\Source\DTOFactory\SourceDetailsDTOFactory;
use App\Application\Source\Message\Command\UpdateSourceCommand;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\ValueObject\Url;
use App\Domain\Source\Repository\SourceRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class UpdateSourceHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private SourceRepository $sourceRepository,
        private SourceDetailsDTOFactory $sourceDetailsDTOFactory,
    ) {
    }

    public function __invoke(UpdateSourceCommand $command): SourceDetailsDTO
    {
        if (!$source = $this->sourceRepository->findOneById($command->sourceId)) {
            throw new EntityNotFoundException($this->translator->trans(id: 'SOURCE.NOT_FOUND', domain: 'validators'));
        }

        if (!$this->security->isGranted(SourceVoter::UPDATE, [$command->userId, $source])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $source->rename($command->name);
        $source->changeUrl(Url::fromNullable($command->url));
        $source->changeActive((bool) $command->active);

        $this->sourceRepository->save($source);

        return $this->sourceDetailsDTOFactory->fromEntity($source);
    }
}
