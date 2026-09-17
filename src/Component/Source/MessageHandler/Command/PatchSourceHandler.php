<?php

declare(strict_types=1);

namespace App\Component\Source\MessageHandler\Command;

use App\Component\Common\Exception\Validation\EntityNotFoundException;
use App\Component\Common\Exception\Validation\UnprocessableEntityException;
use App\Component\Source\DTO\SourceDetailsDTO;
use App\Component\Source\DTOFactory\SourceDetailsDTOFactory;
use App\Component\Source\Message\Command\PatchSourceCommand;
use App\Component\Source\Repository\SourceRepository;
use App\Contract\MessageHandler\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class PatchSourceHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private TranslatorInterface $translator,
        private SourceRepository $sourceRepository,
        private SourceDetailsDTOFactory $sourceDetailsDTOFactory,
    ) {
    }

    public function __invoke(PatchSourceCommand $command): SourceDetailsDTO
    {
        if (!$source = $this->sourceRepository->findOneById($command->id)) {
            throw new EntityNotFoundException($this->translator->trans('SOURCE.NOT_FOUND'));
        }

        if ($command->name === null && $command->url === null && $command->active === null) {
            throw new UnprocessableEntityException($this->translator->trans('SOURCE.AT_LEAST_ONE_FIELD_IS_REQUIRED'));
        }

        $doSave = false;

        if ($command->name !== null) {
            $doSave = true;
            $source->setName($command->name);
        }

        if ($command->url !== null) {
            $doSave = true;
            $source->setUrl($command->url);
        }

        if ($command->active !== null) {
            $doSave = true;
            $source->setActive((bool) $command->active);
        }

        if ($doSave) {
            $this->em->persist($source);
            $this->em->flush();
        }

        return $this->sourceDetailsDTOFactory->fromEntity($source);
    }
}
