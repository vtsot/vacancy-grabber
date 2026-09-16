<?php

declare(strict_types=1);

namespace App\Component\Source\MessageHandler\Command;

use App\Component\Common\Exception\Validation\EntityNotFoundException;
use App\Component\Source\DTO\SourceDetailsDTO;
use App\Component\Source\DTOFactory\SourceDetailsDTOFactory;
use App\Component\Source\Message\Command\UpdateSourceCommand;
use App\Component\Source\Repository\SourceRepository;
use App\Contract\MessageHandler\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class UpdateSourceHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private TranslatorInterface $translator,
        private SourceRepository $sourceRepository,
        private SourceDetailsDTOFactory $sourceDetailsDTOFactory,
    ) {
    }

    public function __invoke(UpdateSourceCommand $command): SourceDetailsDTO
    {
        if (!$source = $this->sourceRepository->findOneById($command->id)) {
            throw new EntityNotFoundException($this->translator->trans('SOURCE.NOT_FOUND'));
        }

        $source
            ->setName($command->name)
            ->setUrl($command->url)
            ->setActive((bool) $command->active);

        $this->em->persist($source);
        $this->em->flush();

        return $this->sourceDetailsDTOFactory->fromEntity($source);
    }
}
