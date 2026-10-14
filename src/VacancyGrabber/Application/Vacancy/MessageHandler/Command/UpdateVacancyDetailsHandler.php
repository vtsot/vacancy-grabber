<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\MessageHandler\Command;

use App\VacancyGrabber\Application\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Application\Exception\Validation\ValidationFailedException;
use App\VacancyGrabber\Application\Security\Voter\VacancyVoter;
use App\VacancyGrabber\Application\Shared\Bus\CommandHandlerInterface;
use App\VacancyGrabber\Application\Shared\Validation\PatchPropertiesValidator;
use App\VacancyGrabber\Application\Vacancy\DTO\VacancyDetailsDTO;
use App\VacancyGrabber\Application\Vacancy\DTOFactory\VacancyDetailsDTOFactory;
use App\VacancyGrabber\Application\Vacancy\Message\Command\UpdateVacancyDetailsCommand;
use App\VacancyGrabber\Application\Vacancy\Service\VacancyFieldWriter;
use App\VacancyGrabber\Domain\Note\Repository\NoteRepository;
use App\VacancyGrabber\Domain\Shared\Exception\EntityNotFoundException;
use App\VacancyGrabber\Domain\User\Repository\UserRepository;
use App\VacancyGrabber\Domain\Vacancy\Repository\VacancyRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_any;
use function array_flip;
use function array_intersect_key;
use function Symfony\Component\String\u;

final readonly class UpdateVacancyDetailsHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private VacancyFieldWriter $vacancyFieldWriter,
        private NoteRepository $noteRepository,
        private UserRepository $userRepository,
        private ValidatorInterface $validator,
        private PatchPropertiesValidator $patchPropertiesValidator,
        private VacancyRepository $vacancyRepository,
        private VacancyDetailsDTOFactory $vacancyDetailsDTOFactory,
    ) {
    }

    private function validateCommand(UpdateVacancyDetailsCommand $command): ?ValidationFailedException
    {
        $violations = [];

        $note    = $command->fields['note'] ?? null;
        $notedAt = $command->fields['notedAt'] ?? null;

        // when not empty note or notedAt
        // both fields are required
        if ($note !== null || $notedAt !== null) {
            if (($errors = $this->validator->validate($note, new Assert\NotBlank()))
                && $errors->count()
            ) {
                $violations[] = [
                    'message' => $errors->get(0)->getMessage(),
                    'path'    => 'note',
                ];
            }

            if (($errors = $this->validator->validate($notedAt, [new Assert\NotBlank()]))
                && $errors->count()
            ) {
                $violations[] = [
                    'message' => $errors->get(0)->getMessage(),
                    'path'    => 'notedAt',
                ];
            }
        }

        return $violations
            ? ValidationFailedException::fromArray(
                $violations,
                $this->translator->trans(id: 'GENERAL.VALIDATION_FAILED', domain: 'validators')
            )
            : null;
    }

    /**
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     * @throws ValidationFailedException
     */
    public function __invoke(UpdateVacancyDetailsCommand $command): VacancyDetailsDTO
    {
        if (!$vacancy = $this->vacancyRepository->findOneById($command->vacancyId)) {
            throw new EntityNotFoundException($this->translator->trans(id: 'VACANCY.NOT_FOUND', domain: 'validators'));
        }

        if (!$this->security->isGranted(VacancyVoter::UPDATE, [$command->userId, $vacancy])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        if ($exception = $this->patchPropertiesValidator->validate($command)) {
            throw $exception;
        }

        if ($exception = $this->validateCommand($command)) {
            throw $exception;
        }

        // update entity
        $values = [];

        foreach ($command->properties as $property) {
            $property          = u($property)->camel()->toString();
            $values[$property] = $command->fields[$property] ?? null;
        }

        $this->vacancyFieldWriter->apply($vacancy, $values);

        // save note
        if (($note = $command->fields['note'] ?? null)
            && ($notedAt = $command->fields['notedAt'] ?? null)
        ) {
            $this->noteRepository->save($vacancy->writeNote(
                author: $command->userId ? $this->userRepository->findOneById($command->userId) : null,
                description: $note,
                notedAt: $notedAt,
            ));
        }

        // a note, note date, resume date or deadline in the request marks the vacancy as viewed
        if (($forViewed = ['note', 'notedAt', 'resumeSentAt', 'deadLineAt'])
            && ($intersect = array_intersect_key($command->fields, array_flip($forViewed)))
            && array_any($intersect, static fn ($value) => $value !== null)
        ) {
            $vacancy->markViewed();
        }

        $this->vacancyRepository->save($vacancy);

        return $this->vacancyDetailsDTOFactory->fromEntity($vacancy);
    }
}
