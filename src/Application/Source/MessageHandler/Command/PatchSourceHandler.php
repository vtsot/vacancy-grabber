<?php

declare(strict_types=1);

namespace App\Application\Source\MessageHandler\Command;

use App\Application\Exception\Security\AccessDeniedException;
use App\Application\Exception\Validation\ValidationFailedException;
use App\Application\Security\Voter\SourceVoter;
use App\Application\Shared\Bus\CommandHandlerInterface;
use App\Application\Source\DTO\SourceDetailsDTO;
use App\Application\Source\DTOFactory\SourceDetailsDTOFactory;
use App\Application\Source\Message\Command\PatchSourceCommand;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Shared\ValueObject\Url;
use App\Domain\Source\Repository\SourceRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function Symfony\Component\String\u;

final readonly class PatchSourceHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private ValidatorInterface $validator,
        private SourceRepository $sourceRepository,
        private SourceDetailsDTOFactory $sourceDetailsDTOFactory,
    ) {
    }

    public function __invoke(PatchSourceCommand $command): SourceDetailsDTO
    {
        if (!$source = $this->sourceRepository->findOneById($command->sourceId)) {
            throw new EntityNotFoundException($this->translator->trans(id: 'SOURCE.NOT_FOUND', domain: 'validators'));
        }

        if (!$this->security->isGranted(SourceVoter::UPDATE, [$command->userId, $source])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        // properties field is  required
        if (($violations = $this->validator->validate($command->properties, new Count(min: 1)))
            && $violations->count()
        ) {
            throw ValidationFailedException::fromArray([[
                'message' => $violations->get(0)->getMessage(),
                'path'    => 'properties',
            ]]);
        }

        // validate required property exists in the command
        foreach ($command->properties as $property) {
            $property = u($property)->camel()->toString();

            if (!property_exists($command, $property)) {
                throw ValidationFailedException::fromArray([[
                    'message' => $this->translator->trans(id: 'GENERAL.PROPERTY_REQUIRED', domain: 'validators'),
                    'path'    => $property,
                ]]);
            }
        }

        // save
        foreach ($command->properties as $property) {
            $property = u($property)->camel()->toString();
            match ($property) {
                'name'   => $source->rename($command->name),
                'url'    => $source->changeUrl(Url::fromNullable($command->url)),
                'active' => $source->changeActive((bool) $command->active),
            };
            $doSave = true;
        }

        if ($doSave ?? false) {
            $this->sourceRepository->save($source);
        }

        return $this->sourceDetailsDTOFactory->fromEntity($source);
    }
}
