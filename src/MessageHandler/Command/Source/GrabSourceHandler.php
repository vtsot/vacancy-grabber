<?php

declare(strict_types=1);

namespace App\MessageHandler\Command\Source;

use App\Contract\MessageHandler\CommandHandlerInterface;
use App\Entity\Company;
use App\Entity\Source;
use App\Entity\Vacancy;
use App\Exception\Validation\EntityNotFoundException;
use App\Message\Command\Source\GrabSourceCommand;
use App\Repository\CompanyRepository;
use App\Repository\SourceRepository;
use App\Repository\VacancyRepository;
use App\Service\VacancyGrabber\GrabberFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class GrabSourceHandler implements CommandHandlerInterface
{
    public function __construct(
        private SourceRepository $sourceRepository,
        private TranslatorInterface $translator,
        private GrabberFactory $grabberFactory,
        private VacancyRepository $vacancyRepository,
        private CompanyRepository $companyRepository,
        private EntityManagerInterface $em,
    ) {
    }

    private function handleCompaniesFromVacancies(array $vacancies): void
    {
        foreach ($vacancies as $vacancyDTO) {
            // create company if not exists
            if (($company = $this->companyRepository->findOneByName($vacancyDTO->company->name)) === null) {
                $company = new Company()
                    ->setName($vacancyDTO->company->name);
                $this->em->persist($company);
            }

            // update company
            $company
                ->setName($vacancyDTO->company->name)
                ->setUrl($vacancyDTO->company->url);
        }

        // save companies
        $this->em->flush();
    }

    private function handleVacancies(Source $source, array $vacancies): void
    {
        foreach ($vacancies as $vacancyDTO) {
            // create vacancy if not exists
            if (($vacancy = $this->vacancyRepository->findOneBySourceIdAndExternalId(
                $source->getId(),
                $vacancyDTO->externalId
            )) === null) {
                $vacancy = new Vacancy()
                    ->setExternalId($vacancyDTO->externalId);
                $this->em->persist($vacancy);
            }

            // update vacancy
            $company = $this->companyRepository->findOneByName($vacancyDTO->company->name);
            $vacancy
                ->setCompany($company)
                ->setSource($source)
                ->setTitle($vacancyDTO->title)
                ->setDescription($vacancyDTO->description)
                ->setExternalId($vacancyDTO->externalId)
                ->setUrl($vacancyDTO->url)
                ->setSalary($vacancyDTO->salary);
        }

        // save vacancies
        $this->em->flush();
    }

    public function __invoke(GrabSourceCommand $command): void
    {
        if (($source = $this->sourceRepository->findOneById($command->sourceId)) === null) {
            throw new EntityNotFoundException(
                $this->translator->trans(id: 'SOURCE_ENTITY.MESSAGE.NOT_FOUND', domain: 'admin-messages')
            );
        }

        $grabber   = $this->grabberFactory->create($source);
        $vacancies = $grabber->grab($source->getUrl());

        // handle
        $this->handleCompaniesFromVacancies($vacancies);
        $this->handleVacancies($source, $vacancies);
    }
}
