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
use App\Service\VacancyGrabber\CompanyDTO;
use App\Service\VacancyGrabber\GrabberFactory;
use App\Service\VacancyGrabber\VacancyDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

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

    private function resolveCompany(CompanyDTO $companyDTO): Company
    {
        $company = $this->companyRepository->findOneByName($companyDTO->name);

        if ($company === null) {
            $company = new Company()
                ->setName($companyDTO->name);
            $this->em->persist($company);
        }

        return $company;
    }

    private function resolveVacancy(Source $source, VacancyDTO $vacancyDTO): Vacancy
    {
        $vacancy = $this->vacancyRepository->findOneBySourceIdAndExternalId($source->getId(), $vacancyDTO->externalId);

        if ($vacancy === null) {
            $vacancy = new Vacancy()
                ->setSource($source)
                ->setExternalId($vacancyDTO->externalId);
            $this->em->persist($vacancy);
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
            $company = $vacancyDTO->company ? $this->resolveCompany($vacancyDTO->company) : null;
            $company?->setUrl($vacancyDTO->company?->url);

            // save company
            $this->em->flush();

            $vacancy = $this->resolveVacancy($source, $vacancyDTO);
            $vacancy
                ->setCompany($company)
                ->setTitle($vacancyDTO->title)
                ->setDescription($vacancyDTO->description)
                ->setExternalId($vacancyDTO->externalId)
                ->setUrl($vacancyDTO->url)
                ->setSalary($vacancyDTO->salary);

            // save vacancy
            $this->em->flush();
        }
    }
}
