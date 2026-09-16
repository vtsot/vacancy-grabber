<?php

declare(strict_types=1);

namespace App\Component\Source\MessageHandler\Command;

use App\Component\Source\DTO\SourceDetailsDTO;
use App\Component\Source\DTOFactory\SourceDetailsDTOFactory;
use App\Component\Source\Message\Command\UpdateSourceCommand;
use App\Contract\MessageHandler\CommandHandlerInterface;
use App\Entity\Source;
use Doctrine\ORM\EntityManagerInterface;

final readonly class UpdateSourceHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private SourceDetailsDTOFactory $sourceDetailsDTOFactory,
    ) {
    }

    public function __invoke(UpdateSourceCommand $command): SourceDetailsDTO
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
