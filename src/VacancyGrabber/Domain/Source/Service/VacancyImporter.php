<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Domain\Source\Service;

use App\VacancyGrabber\Domain\Company\Entity\Company;
use App\VacancyGrabber\Domain\Company\Repository\CompanyRepository;
use App\VacancyGrabber\Domain\GrabLog\Entity\GrabLog;
use App\VacancyGrabber\Domain\GrabLog\Repository\GrabLogRepository;
use App\VacancyGrabber\Domain\Shared\Exception\InvalidArgumentException;
use App\VacancyGrabber\Domain\Shared\ValueObject\Url;
use App\VacancyGrabber\Domain\Source\Entity\Source;
use App\VacancyGrabber\Domain\Source\Grabber\GrabbedCompany;
use App\VacancyGrabber\Domain\Source\Grabber\GrabbedVacancy;
use App\VacancyGrabber\Domain\User\Entity\User;
use App\VacancyGrabber\Domain\Vacancy\Entity\Vacancy;
use App\VacancyGrabber\Domain\Vacancy\Repository\VacancyRepository;
use App\VacancyGrabber\Domain\Vacancy\ValueObject\ExternalId;

/**
 * Turns grabbed listings into vacancies of a source: reuses companies by name, skips vacancies already
 * imported for this owner and source, and logs the run when something new was found.
 */
final readonly class VacancyImporter
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private VacancyRepository $vacancyRepository,
        private GrabLogRepository $grabLogRepository,
    ) {
    }

    /**
     * @param iterable<GrabbedVacancy> $grabbedVacancies
     */
    public function import(?User $owner, Source $source, iterable $grabbedVacancies): ImportResult
    {
        $companiesCreated = 0;
        $vacanciesCreated = 0;

        foreach ($grabbedVacancies as $grabbed) {
            $externalId = ExternalId::fromNullable($grabbed->externalId);

            // without an external id a listing cannot be told apart from earlier imports
            if ($externalId === null || $this->isImported($owner, $source, $externalId)) {
                continue;
            }

            $company = $grabbed->company ? $this->resolveCompany($grabbed->company, $companiesCreated) : null;

            $this->vacancyRepository->save(Vacancy::grabbed($owner, $source, $company, $grabbed));
            $vacanciesCreated++;
        }

        if ($vacanciesCreated > 0) {
            $this->grabLogRepository->save(GrabLog::record($owner, $source, $companiesCreated, $vacanciesCreated));
        }

        return new ImportResult($companiesCreated, $vacanciesCreated);
    }

    private function isImported(?User $owner, Source $source, ExternalId $externalId): bool
    {
        return $this->vacancyRepository->findOneByUserIdAndSourceIdAndExternalId(
            userId: $owner?->getId(),
            sourceId: $source->getId(),
            externalId: $externalId->toString(),
        ) !== null;
    }

    private function resolveCompany(GrabbedCompany $grabbed, int &$companiesCreated): ?Company
    {
        if ($grabbed->name === null || $grabbed->name === '') {
            return null;
        }

        if ($company = $this->companyRepository->findOneByName($grabbed->name)) {
            return $company;
        }

        try {
            $url = Url::fromNullable($grabbed->url);
        } catch (InvalidArgumentException) {
            $url = null;
        }

        $company = Company::create($grabbed->name, $url);
        $this->companyRepository->save($company);
        $companiesCreated++;

        return $company;
    }
}
