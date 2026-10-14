<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Vacancy\MessageHandler\Command;

use App\Contract\MessageHandler\CommandHandlerInterface;
use App\VacancyGrabber\Application\Component\Vacancy\DTO\VacancyDetailsDTO;
use App\VacancyGrabber\Application\Component\Vacancy\DTOFactory\VacancyDetailsDTOFactory;
use App\VacancyGrabber\Application\Component\Vacancy\Message\Command\PatchVacancyCommand;
use App\VacancyGrabber\Application\Component\Vacancy\Security\Voter\VacancyVoter;
use App\VacancyGrabber\Application\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Application\Exception\Validation\EntityNotFoundException;
use App\VacancyGrabber\Application\Exception\Validation\ValidationFailedException;
use App\VacancyGrabber\Domain\Vacancy\Repository\VacancyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function property_exists;
use function Symfony\Component\String\u;
use function ucfirst;

final readonly class PatchVacancyHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
        private VacancyRepository $vacancyRepository,
        private VacancyDetailsDTOFactory $vacancyDetailsDTOFactory,
    ) {
    }

    public function __invoke(PatchVacancyCommand $command): VacancyDetailsDTO
    {
        if (!$vacancy = $this->vacancyRepository->findOneById($command->vacancyId)) {
            throw new EntityNotFoundException($this->translator->trans(id: 'VACANCY.NOT_FOUND', domain: 'validators'));
        }

        if (!$this->security->isGranted(VacancyVoter::UPDATE, [$command->userId, $vacancy])) {
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
        foreach ($command->properties as $property) {
            $property = u($property)->camel()->toString();
            $vacancy->{'set' . ucfirst($property)}($command->{$property});
            $doSave = true;
        }

        // when resumeSentAt is not null
        // mark vacancy as viewed
        if ($command->resumeSentAt !== null
            || $command->deadLineAt !== null
        ) {
            $vacancy->setViewed(true);
            $doSave = true;
        }

        // save
        if ($doSave ?? false) {
            $this->em->flush();
        }

        return $this->vacancyDetailsDTOFactory->fromEntity($vacancy);
    }
}
