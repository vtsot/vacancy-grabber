<?php

declare(strict_types=1);

namespace App\MessageHandler\Command\Source;

use App\Contract\MessageHandler\CommandHandlerInterface;
use App\Entity\Company;
use App\Entity\GrabLog;
use App\Entity\Source;
use App\Entity\Vacancy;
use App\Exception\Validation\EntityNotFoundException;
use App\Message\Command\Source\GrabSourceCommand;
use App\Repository\CompanyRepository;
use App\Repository\SourceRepository;
use App\Repository\VacancyRepository;
use App\Service\VacancyGrabber\CompanyDTO;
use App\Service\VacancyGrabber\GrabberFactory;
use App\Service\VacancyGrabber\VacancyDTO;
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
    public function __invoke(GrabSourceCommand $command): void
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

        $gabLog = new GrabLog()
            ->setSource($source)
            ->setCounterCompanyCreated($this->counterCompanyCreated)
            ->setCounterVacancyCreated($this->counterVacancyCreated);

        $this->em->persist($gabLog);
        $this->em->flush();
    }
}
