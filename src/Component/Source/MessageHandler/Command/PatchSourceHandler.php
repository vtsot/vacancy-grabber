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

use function ucfirst;

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
            throw new UnprocessableEntityException($this->translator->trans(
                'GENERAL.AT_LEAST_ONE_OF_PROPERTY_NOT_NULL',
                ['%properties%' => '"name", "url", "active"'],
                'validators'
            ));
        }

        foreach (['name', 'url', 'active'] as $prop) {
            if ($command->$prop !== null) {
                $source->{'set' . ucfirst($prop)}($command->$prop);
                $doSave = true;
            }
        }

        if ($doSave ?? false) {
            $this->em->flush();
        }

        return $this->sourceDetailsDTOFactory->fromEntity($source);
    }
}
