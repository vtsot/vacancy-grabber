<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Source\MessageHandler\Command;

use App\Component\General\Exception\Security\AccessDeniedException;
use App\Contract\MessageHandler\CommandHandlerInterface;
use App\VacancyGrabber\Application\Source\DTO\SourceDetailsDTO;
use App\VacancyGrabber\Application\Source\DTOFactory\SourceDetailsDTOFactory;
use App\VacancyGrabber\Application\Source\Message\Command\CreateSourceCommand;
use App\VacancyGrabber\Application\Source\Security\Voter\SourceVoter;
use App\VacancyGrabber\Domain\Source\Entity\Source;
use App\VacancyGrabber\Domain\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class CreateSourceHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private EntityManagerInterface $em,
        private SourceDetailsDTOFactory $sourceDetailsDTOFactory,
    ) {
    }

    /**
     * @throws ORMException
     */
    public function __invoke(CreateSourceCommand $command): SourceDetailsDTO
    {
        if (!$this->security->isGranted(SourceVoter::CREATE, [$command->userId, Source::class])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $source = new Source()
            ->setUser($command->userId ? $this->em->getReference(User::class, $command->userId) : null)
            ->setName($command->name)
            ->setUrl($command->url)
            ->setActive((bool) $command->active);

        $this->em->persist($source);
        $this->em->flush();

        return $this->sourceDetailsDTOFactory->fromEntity($source);
    }
}
