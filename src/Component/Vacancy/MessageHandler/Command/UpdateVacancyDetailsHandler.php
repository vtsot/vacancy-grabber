<?php

declare(strict_types=1);

namespace App\Component\Vacancy\MessageHandler\Command;

use App\Component\General\Exception\Security\AccessDeniedException;
use App\Component\General\Exception\Validation\EntityNotFoundException;
use App\Component\General\Exception\Validation\ValidationFailedException;
use App\Component\General\Validator\PatchPropertiesValidator;
use App\Component\Note\Entity\Note;
use App\Component\User\Entity\User;
use App\Component\Vacancy\DTO\VacancyDetailsDTO;
use App\Component\Vacancy\DTOFactory\VacancyDetailsDTOFactory;
use App\Component\Vacancy\Message\Command\UpdateVacancyDetailsCommand;
use App\Component\Vacancy\Repository\VacancyRepository;
use App\Component\Vacancy\Security\Voter\VacancyVoter;
use App\Contract\MessageHandler\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_flip;
use function array_intersect_key;
use function Symfony\Component\String\u;
use function ucfirst;

final readonly class UpdateVacancyDetailsHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private EntityManagerInterface $em,
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
     * @throws ORMException
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
        foreach ($command->properties as $property) {
            $property = u($property)->camel()->toString();
            $setter   = 'set' . ucfirst($property);
            $value    = $command->fields[$property] ?? null;

            //            // call entity setter
            //            if (method_exists($vacancy, $setter)) {
            //                $doSave = true;
            //                $vacancy->{$setter}($value);
            //            }
            $doSave = true;
            match ($property) {
                'title'        => $vacancy->setTitle($value),
                'description'  => $vacancy->setDescription($value),
                'externalId'   => $vacancy->setExternalId($value),
                'url'          => $vacancy->setUrl($value),
                'salary'       => $vacancy->setSalary($value),
                'viewed'       => $vacancy->setViewed((bool) $value),
                'resumeSentAt' => $vacancy->setResumeSentAt($value),
                'status'       => $value ? $vacancy->setStatus($value) : null,
                default        => static fn () => null,
            };
        }

        // save note
        if (($note = $command->fields['note'] ?? null)
            && ($notedAt = $command->fields['notedAt'] ?? null)
        ) {
            // create note
            $note = new Note()
                ->setUser($command->userId ? $this->em->getReference(User::class, $command->userId) : null)
                ->setVacancy($vacancy)
                ->setNotedAt($notedAt)
                ->setDescription($note);

            $doSave = true;
            $this->em->persist($note);
        }

        // check if fields contain any of the keys from
        if (($forViewed = ['note', 'notedAt', 'resumeSentAt'])
            && !empty(array_intersect_key($command->fields, array_flip($forViewed)))
        ) {
            $doSave = true;
            $vacancy->setViewed(true);
        }

        // save
        if ($doSave ?? false) {
            $this->em->flush();
        }

        return $this->vacancyDetailsDTOFactory->fromEntity($vacancy);
    }
}
