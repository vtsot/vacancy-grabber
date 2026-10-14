<?php

declare(strict_types=1);

namespace App\Application\Vacancy\MessageHandler\Command;

use App\Application\Exception\Security\AccessDeniedException;
use App\Application\Exception\Validation\ValidationFailedException;
use App\Application\Security\Voter\VacancyVoter;
use App\Application\Shared\Bus\CommandHandlerInterface;
use App\Application\Vacancy\DTO\VacancyDetailsDTO;
use App\Application\Vacancy\DTOFactory\VacancyDetailsDTOFactory;
use App\Application\Vacancy\Message\Command\PatchVacancyCommand;
use App\Application\Vacancy\Service\VacancyFieldWriter;
use App\Domain\Shared\Exception\EntityNotFoundException;
use App\Domain\Vacancy\Repository\VacancyRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function property_exists;
use function Symfony\Component\String\u;

final readonly class PatchVacancyHandler implements CommandHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private VacancyFieldWriter $vacancyFieldWriter,
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
        $values = [];

        foreach ($command->properties as $property) {
            $property          = u($property)->camel()->toString();
            $values[$property] = $command->{$property};
        }

        $this->vacancyFieldWriter->apply($vacancy, $values);

        // a resume date or deadline in the request marks the vacancy as viewed
        // even when the date itself is not among the patched properties
        if ($command->resumeSentAt !== null
            || $command->deadLineAt !== null
        ) {
            $vacancy->markViewed();
        }

        $this->vacancyRepository->save($vacancy);

        return $this->vacancyDetailsDTOFactory->fromEntity($vacancy);
    }
}
