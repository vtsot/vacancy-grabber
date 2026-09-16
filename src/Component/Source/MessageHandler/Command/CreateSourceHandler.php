<?php

declare(strict_types=1);

namespace App\Component\Source\MessageHandler\Command;

use App\Component\Source\DTO\SourceDetailsDTO;
use App\Component\Source\DTOFactory\SourceDetailsDTOFactory;
use App\Component\Source\Entity\Source;
use App\Component\Source\Message\Command\CreateSourceCommand;
use App\Contract\MessageHandler\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CreateSourceHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private SourceDetailsDTOFactory $sourceDetailsDTOFactory,
    ) {
    }

    public function __invoke(CreateSourceCommand $command): SourceDetailsDTO
    {
        $source = new Source()
            ->setName($command->name)
            ->setUrl($command->url)
            ->setActive((bool) $command->active);

        $this->em->persist($source);
        $this->em->flush();

        return $this->sourceDetailsDTOFactory->fromEntity($source);
    }
}
