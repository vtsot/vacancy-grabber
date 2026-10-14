<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Vacancy\EntityFactory;

use App\VacancyGrabber\Application\Component\Vacancy\Message\Command\CreateVacancyCommand;
use App\VacancyGrabber\Application\Component\Vacancy\Message\Command\UpdateVacancyCommand;
use App\VacancyGrabber\Application\Exception\Validation\EntityNotFoundException;
use App\VacancyGrabber\Application\Exception\Validation\ValidationFailedException;
use App\VacancyGrabber\Domain\Company\Entity\Company;
use App\VacancyGrabber\Domain\Company\Repository\CompanyRepository;
use App\VacancyGrabber\Domain\Source\Entity\Source;
use App\VacancyGrabber\Domain\Source\Repository\SourceRepository;
use App\VacancyGrabber\Domain\Vacancy\Entity\Vacancy;
use App\VacancyGrabber\Domain\Vacancy\Entity\VacancyStatus;
use App\VacancyGrabber\Domain\Vacancy\Repository\VacancyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class VacancyEntityFactory
{
    public function __construct(
        private EntityManagerInterface $em,
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
     * @throws ORMException
     */
    public function processCommand(
        CreateVacancyCommand|UpdateVacancyCommand $command,
    ): Vacancy {
        // resolve vacancy
        $vacancy = $command instanceof CreateVacancyCommand
            ? new Vacancy()
            : $this->vacancyRepository->findOneById($command->vacancyId);

        if (!$vacancy) {
            throw new EntityNotFoundException(
                $this->translator->trans('VACANCY.NOT_FOUND', domain: 'validators')
            );
        }

        if ($exception = $this->validateCommand($vacancy, $command)) {
            throw $exception;
        }

        $vacancy
            ->setTitle($command->title)
            ->setDescription($command->description)
            ->setExternalId($command->externalId)
            ->setUrl($command->url)
            ->setSalary($command->salary)
            ->setSource($command->sourceId ? $this->em->getReference(Source::class, $command->sourceId) : null)
            ->setCompany($command->companyId ? $this->em->getReference(Company::class, $command->companyId) : null)
            ->setStatus($command->status ?? VacancyStatus::Pending)
            ->setViewed($command->viewed)
            ->setResumeSentAt($command->resumeSentAt)
            ->setDeadLineAt($command->deadLineAt);

        return $vacancy;
    }
}
