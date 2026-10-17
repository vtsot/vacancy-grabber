<?php

declare(strict_types=1);

namespace App\Tests\Suite\Unit\Domain;

use App\Domain\Company\Entity\Company;
use App\Domain\Company\Repository\CompanyRepository;
use App\Domain\GrabLog\Entity\GrabLog;
use App\Domain\GrabLog\Repository\GrabLogRepository;
use App\Domain\Source\Entity\Source;
use App\Domain\Source\Grabber\GrabbedCompany;
use App\Domain\Source\Grabber\GrabbedVacancy;
use App\Domain\Source\Service\VacancyImporter;
use App\Domain\Vacancy\Entity\Vacancy;
use App\Domain\Vacancy\Repository\VacancyRepository;
use Codeception\Test\Unit;
use ReflectionProperty;
use Symfony\Component\Uid\Uuid;

use function in_array;

final class VacancyImporterTest extends Unit
{
    /** @var array<string, Company> */
    private array $companies = [];

    /** @var list<Vacancy> */
    private array $savedVacancies = [];

    /** @var list<GrabLog> */
    private array $savedLogs = [];

    /** @var list<string> */
    private array $existingExternalIds = [];

    public function testImportsNewVacanciesReusingCompaniesAndLogsTheRun(): void
    {
        $result = $this->importer()->import(null, $this->source(), [
            $this->grabbed('1', 'Acme'),
            $this->grabbed('2', 'Acme'),
            $this->grabbed('3', null),
        ]);

        self::assertSame(1, $result->companiesCreated);
        self::assertSame(3, $result->vacanciesCreated);
        self::assertCount(3, $this->savedVacancies);
        self::assertSame($this->savedVacancies[0]->getCompany(), $this->savedVacancies[1]->getCompany());
        self::assertNull($this->savedVacancies[2]->getCompany());

        self::assertCount(1, $this->savedLogs);
        self::assertSame(1, $this->savedLogs[0]->getCounterCompanyCreated());
        self::assertSame(3, $this->savedLogs[0]->getCounterVacancyCreated());
        self::assertSame(0, $this->savedLogs[0]->getCounterVacancyUpdated());
    }

    public function testLogsARunThatOnlyUpdatedVacancies(): void
    {
        $this->existingExternalIds = ['1'];

        $result = $this->importer()->import(null, $this->source(), [$this->grabbed('1', 'Acme')], vacanciesUpdated: 2);

        self::assertSame(0, $result->vacanciesCreated);
        self::assertCount(1, $this->savedLogs);
        self::assertSame(0, $this->savedLogs[0]->getCounterVacancyCreated());
        self::assertSame(2, $this->savedLogs[0]->getCounterVacancyUpdated());
    }

    public function testSkipsAlreadyImportedAndUnidentifiableListings(): void
    {
        $this->existingExternalIds = ['1'];

        $result = $this->importer()->import(null, $this->source(), [
            $this->grabbed('1', 'Acme'),
            $this->grabbed(null, 'Acme'),
        ]);

        self::assertSame(0, $result->vacanciesCreated);
        self::assertSame(0, $result->companiesCreated);
        self::assertSame([], $this->savedVacancies);
        self::assertSame([], $this->savedLogs, 'no log when nothing new was found');
    }

    private function importer(): VacancyImporter
    {
        $companies = $this->createMock(CompanyRepository::class);
        $companies->method('findOneByName')->willReturnCallback(fn (string $name) => $this->companies[$name] ?? null);
        $companies->method('save')->willReturnCallback(function (Company $company): void {
            $this->companies[$company->getName()] = $company;
        });

        $vacancies = $this->createMock(VacancyRepository::class);
        $vacancies->method('findOneByUserIdAndSourceIdAndExternalId')->willReturnCallback(
            fn ($userId, $sourceId, string $externalId) => in_array($externalId, $this->existingExternalIds, true) ? new Vacancy() : null
        );
        $vacancies->method('save')->willReturnCallback(function (Vacancy $vacancy): void {
            $this->savedVacancies[] = $vacancy;
        });

        $logs = $this->createMock(GrabLogRepository::class);
        $logs->method('save')->willReturnCallback(function (GrabLog $log): void {
            $this->savedLogs[] = $log;
        });

        return new VacancyImporter($companies, $vacancies, $logs);
    }

    private function source(): Source
    {
        $source = new Source();
        new ReflectionProperty($source, 'id')->setValue($source, Uuid::v7());

        return $source;
    }

    private function grabbed(?string $externalId, ?string $company): GrabbedVacancy
    {
        return new GrabbedVacancy(
            externalId: $externalId,
            url: 'https://jobs.example.com/' . $externalId,
            title: 'Vacancy ' . $externalId,
            description: null,
            salary: null,
            company: $company ? new GrabbedCompany(url: 'https://example.com/' . $company, name: $company) : null,
        );
    }
}
