<?php

declare(strict_types=1);

namespace App\Component\Vacancy\MessageHandler\Command;

use App\Component\General\Exception\Validation\EntityNotFoundException;
use App\Component\General\Exception\Validation\ValidationFailedException;
use App\Component\Vacancy\DTO\VacancyDetailsDTO;
use App\Component\Vacancy\DTOFactory\VacancyDetailsDTOFactory;
use App\Component\Vacancy\Message\Command\PatchVacancyCommand;
use App\Component\Vacancy\Repository\VacancyRepository;
use App\Contract\MessageHandler\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
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
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
        private TranslatorInterface $translator,
        private VacancyRepository $vacancyRepository,
        private VacancyDetailsDTOFactory $vacancyDetailsDTOFactory,
    ) {
    }

    public function __invoke(PatchVacancyCommand $command): VacancyDetailsDTO
    {
        if (!$vacancy = $this->vacancyRepository->findOneById($command->id)) {
            throw new EntityNotFoundException($this->translator->trans('VACANCY.NOT_FOUND'));
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
            $vacancy->{'set' . ucfirst($property)}($command->{$property});
            $doSave = true;
        }

        if ($doSave ?? false) {
            $this->em->flush();
        }

        return $this->vacancyDetailsDTOFactory->fromEntity($vacancy);
    }
}
