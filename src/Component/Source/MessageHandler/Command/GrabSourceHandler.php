<?php

declare(strict_types=1);

namespace App\Component\Source\MessageHandler\Command;

use App\Component\Common\Exception\Validation\EntityNotFoundException;
use App\Component\Company\Entity\Company;
use App\Component\Company\Repository\CompanyRepository;
use App\Component\GrabLog\Entity\GrabLog;
use App\Component\Source\Entity\Source;
use App\Component\Source\Message\Command\GrabSourceCommand;
use App\Component\Source\Repository\SourceRepository;
use App\Component\Vacancy\Entity\Vacancy;
use App\Component\Vacancy\Repository\VacancyRepository;
use App\Component\VacancyGrabber\DTO\CompanyDTO;
use App\Component\VacancyGrabber\DTO\VacancyDTO;
use App\Component\VacancyGrabber\Factory\GrabberFactory;
use App\Contract\MessageHandler\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

final class GrabSourceHandler implements CommandHandlerInterface
{
    private int $counterCompanyCreated = 0;
    private int $counterVacancyCreated = 0;

    public function __construct(
        private readonly SourceRepository $sourceRepository,
        private readonly TranslatorInterface $translator,
        private readonly GrabberFactory $grabberFactory,
        private readonly VacancyRepository $vacancyRepository,
        private readonly CompanyRepository $companyRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    private function saveCompany(CompanyDTO $companyDTO): Company
    {
        $company = $this->companyRepository->findOneByName($companyDTO->name);

        if ($company === null) {
            $company = new Company()
                ->setName($companyDTO->name)
                ->setUrl($companyDTO->url);

            // save
            $this->em->persist($company);
            $this->em->flush();
            $this->counterCompanyCreated++;
        }

        return $company;
    }

    private function saveVacancy(Source $source, ?Company $company, VacancyDTO $vacancyDTO): Vacancy
    {
        $vacancy = $this->vacancyRepository->findOneBySourceIdAndExternalId($source->getId(), $vacancyDTO->externalId);

        if ($vacancy === null) {
            $vacancy = new Vacancy()
                ->setSource($source)
                ->setExternalId($vacancyDTO->externalId)
                ->setCompany($company)
                ->setTitle($vacancyDTO->title)
                ->setDescription($vacancyDTO->description)
                ->setExternalId($vacancyDTO->externalId)
                ->setUrl($vacancyDTO->url)
                ->setSalary($vacancyDTO->salary);

            // save
            $this->em->persist($vacancy);
            $this->em->flush();
            $this->counterVacancyCreated++;
        }

        return $vacancy;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(GrabSourceCommand $command): int
    {
        if (($source = $this->sourceRepository->findOneById($command->sourceId)) === null) {
            throw new EntityNotFoundException(
                $this->translator->trans(id: 'SOURCE_ENTITY.MESSAGE.NOT_FOUND', domain: 'admin-messages')
            );
        }

        $grabber   = $this->grabberFactory->create($source);
        $vacancies = $grabber->grab($source->getUrl());

        // handle vacancies
        foreach ($vacancies as $vacancyDTO) {
            $company = $vacancyDTO->company ? $this->saveCompany($vacancyDTO->company) : null;
            $vacancy = $this->saveVacancy($source, $company, $vacancyDTO);
        }

        if ($this->counterVacancyCreated) {
            $gabLog = new GrabLog()
                ->setSource($source)
                ->setCounterCompanyCreated($this->counterCompanyCreated)
                ->setCounterVacancyCreated($this->counterVacancyCreated);

            $this->em->persist($gabLog);
            $this->em->flush();
        }

        return $this->counterVacancyCreated;
    }
}
