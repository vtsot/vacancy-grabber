<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\EntityFactory;

use App\VacancyGrabber\Application\Exception\Validation\ValidationFailedException;
use App\VacancyGrabber\Application\Vacancy\Message\Command\CreateVacancyCommand;
use App\VacancyGrabber\Application\Vacancy\Message\Command\UpdateVacancyCommand;
use App\VacancyGrabber\Domain\Company\Repository\CompanyRepository;
use App\VacancyGrabber\Domain\Shared\Exception\EntityNotFoundException;
use App\VacancyGrabber\Domain\Shared\ValueObject\Url;
use App\VacancyGrabber\Domain\Source\Repository\SourceRepository;
use App\VacancyGrabber\Domain\User\Repository\UserRepository;
use App\VacancyGrabber\Domain\Vacancy\Entity\Vacancy;
use App\VacancyGrabber\Domain\Vacancy\Entity\VacancyStatus;
use App\VacancyGrabber\Domain\Vacancy\Repository\VacancyRepository;
use App\VacancyGrabber\Domain\Vacancy\ValueObject\ExternalId;
use App\VacancyGrabber\Domain\Vacancy\ValueObject\Salary;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class VacancyEntityFactory
{
    public function __construct(
        private UserRepository $userRepository,
        private TranslatorInterface $translator,
        private SourceRepository $sourceRepository,
        private CompanyRepository $companyRepository,
        private VacancyRepository $vacancyRepository,
    ) {
    }

    private function validateCommand(
        Vacancy $vacancy,
        CreateVacancyCommand|UpdateVacancyCommand $command,
    ): ?ValidationFailedException {
        $violations = [];

        if ($command->sourceId
            && !$this->sourceRepository->findOneById($command->sourceId)
        ) {
            $violations[] = [
                'message' => $this->translator->trans(id: 'SOURCE.NOT_FOUND', domain: 'validators'),
                'path'    => 'source',
            ];
        }

        if ($command->companyId
            && !$this->companyRepository->findOneById($command->companyId)
        ) {
            $violations[] = [
                'message' => $this->translator->trans(id: 'COMPANY.NOT_FOUND', domain: 'validators'),
                'path'    => 'company',
            ];
        }

        return $violations
            ? ValidationFailedException::fromArray(
                $violations,
                $this->translator->trans(id: 'GENERAL.VALIDATION_FAILED', domain: 'validators')
            )
            : null;
    }

    /**
     * @throws ValidationFailedException
     */
    public function processCommand(
        CreateVacancyCommand|UpdateVacancyCommand $command,
    ): Vacancy {
        // resolve vacancy
        $vacancy = $command instanceof CreateVacancyCommand
            ? Vacancy::create(
                owner: $command->userId ? $this->userRepository->findOneById($command->userId) : null,
                title: $command->title,
            )
            : $this->vacancyRepository->findOneById($command->vacancyId);

        if (!$vacancy) {
            throw new EntityNotFoundException(
                $this->translator->trans('VACANCY.NOT_FOUND', domain: 'validators')
            );
        }

        if ($exception = $this->validateCommand($vacancy, $command)) {
            throw $exception;
        }

        $vacancy->rename($command->title);
        $vacancy->describe($command->description);
        $vacancy->changeExternalId(ExternalId::fromNullable($command->externalId));
        $vacancy->changeUrl(Url::fromNullable($command->url));
        $vacancy->changeSalary(Salary::fromNullable($command->salary));
        $vacancy->moveToSource($command->sourceId ? $this->sourceRepository->findOneById($command->sourceId) : null);
        $vacancy->assignCompany($command->companyId ? $this->companyRepository->findOneById($command->companyId) : null);
        $vacancy->changeStatus($command->status ?? VacancyStatus::Pending);
        $vacancy->markResumeSent($command->resumeSentAt);
        $vacancy->scheduleDeadline($command->deadLineAt);
        // a full update states "viewed" explicitly, so it wins over the dates above
        $vacancy->changeViewed((bool) $command->viewed);

        return $vacancy;
    }
}
