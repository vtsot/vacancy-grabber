<?php

declare(strict_types=1);

namespace App\Application\Note\MessageHandler\Command;

use App\Application\Exception\Security\AccessDeniedException;
use App\Application\Exception\Validation\ValidationFailedException;
use App\Application\Note\DTO\NoteDetailsDTO;
use App\Application\Note\DTOFactory\NoteDetailsDTOFactory;
use App\Application\Note\Message\Command\PatchNoteCommand;
use App\Application\Note\Service\NoteFieldWriter;
use App\Application\Security\Voter\NoteVoter;
use App\Application\Shared\Bus\CommandHandlerInterface;
use App\Domain\Note\Repository\NoteRepository;
use App\Domain\Shared\Exception\EntityNotFoundException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function property_exists;
use function Symfony\Component\String\u;

final readonly class PatchNoteHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private NoteFieldWriter $noteFieldWriter,
        private ValidatorInterface $validator,
        private NoteRepository $noteRepository,
        private NoteDetailsDTOFactory $noteDetailsDTOFactory,
    ) {
    }

    public function __invoke(PatchNoteCommand $command): NoteDetailsDTO
    {
        if (!$note = $this->noteRepository->findOneById($command->noteId)) {
            throw new EntityNotFoundException($this->translator->trans(id: 'NOTE.NOT_FOUND', domain: 'validators'));
        }

        if (!$this->security->isGranted(NoteVoter::UPDATE, [$command->userId, $note])) {
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

        // validate each property
        foreach ($command->properties as $index => $property) {
            $violations = $this->validator->validate($property, new NotBlank());

            if ($violations->count()) {
                throw ValidationFailedException::fromArray([[
                    'message' => $violations->get(0)->getMessage(),
                    'path'    => 'properties' . $index,
                ]]);
            }
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

        // update
        $values = [];

        foreach ($command->properties as $property) {
            $property          = u($property)->camel()->toString();
            $values[$property] = $command->{$property};
        }

        $this->noteFieldWriter->apply($note, $values);

        $this->noteRepository->save($note);

        return $this->noteDetailsDTOFactory->fromEntity($note);
    }
}
