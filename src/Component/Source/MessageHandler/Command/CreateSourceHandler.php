<?php

declare(strict_types=1);

namespace App\Component\Source\MessageHandler\Command;

use App\Component\General\Exception\Security\AccessDeniedException;
use App\Component\Source\DTO\SourceDetailsDTO;
use App\Component\Source\DTOFactory\SourceDetailsDTOFactory;
use App\Component\Source\Entity\Source;
use App\Component\Source\Message\Command\CreateSourceCommand;
use App\Component\Source\Security\Voter\SourceVoter;
use App\Component\User\Entity\User;
use App\Contract\MessageHandler\CommandHandlerInterface;
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
