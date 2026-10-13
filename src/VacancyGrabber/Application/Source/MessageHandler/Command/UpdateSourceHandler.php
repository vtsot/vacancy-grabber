<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Source\MessageHandler\Command;

use App\Component\General\Exception\Security\AccessDeniedException;
use App\Component\General\Exception\Validation\EntityNotFoundException;
use App\Contract\MessageHandler\CommandHandlerInterface;
use App\VacancyGrabber\Application\Source\DTO\SourceDetailsDTO;
use App\VacancyGrabber\Application\Source\DTOFactory\SourceDetailsDTOFactory;
use App\VacancyGrabber\Application\Source\Message\Command\UpdateSourceCommand;
use App\VacancyGrabber\Application\Source\Security\Voter\SourceVoter;
use App\VacancyGrabber\Domain\Source\Repository\SourceRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class UpdateSourceHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private EntityManagerInterface $em,
        private SourceRepositoryInterface $sourceRepository,
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

        $source
            ->setName($command->name)
            ->setUrl($command->url)
            ->setActive((bool) $command->active);

        $this->em->flush();

        return $this->sourceDetailsDTOFactory->fromEntity($source);
    }
}
