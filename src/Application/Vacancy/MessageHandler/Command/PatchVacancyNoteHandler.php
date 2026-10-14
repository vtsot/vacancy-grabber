<?php

declare(strict_types=1);

namespace App\Application\Vacancy\MessageHandler\Command;

use App\Application\Exception\Security\AccessDeniedException;
use App\Application\Exception\Validation\ValidationFailedException;
use App\Application\Security\Voter\VacancyNoteVoter;
use App\Application\Shared\Bus\CommandHandlerInterface;
use App\Application\Vacancy\DTO\VacancyNoteDTO;
use App\Application\Vacancy\DTOFactory\VacancyNoteDTOFactory;
use App\Application\Vacancy\Message\Command\PatchVacancyNoteCommand;
use App\Domain\Note\Repository\NoteRepository;
use App\Domain\Shared\Exception\EntityNotFoundException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function property_exists;
use function Symfony\Component\String\u;

final readonly class PatchVacancyNoteHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private ValidatorInterface $validator,
        private NoteRepository $noteRepository,
        private VacancyNoteDTOFactory $vacancyNoteDTOFactory,
    ) {
    }

    public function __invoke(PatchVacancyNoteCommand $command): VacancyNoteDTO
    {
        if (!($note = $this->noteRepository->findOneByUserIdAndNoteIdAndVacancyId($command->userId, $command->noteId, $command->vacancyId))) {
            throw new EntityNotFoundException($this->translator->trans(id: 'NOTE.NOT_FOUND', domain: 'validators'));
        }

        if (!$this->security->isGranted(VacancyNoteVoter::UPDATE, [$command->userId, $note])) {
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

        // save
        foreach ($command->properties as $property) {
            $property = u($property)->camel()->toString();
            match ($property) {
                'notedAt'     => $note->changeNotedAt($command->notedAt),
                'description' => $note->rewrite($command->description),
            };
            $doSave = true;
        }

        if ($doSave ?? false) {
            $this->noteRepository->save($note);
        }

        return $this->vacancyNoteDTOFactory->fromEntity($note);
    }
}
