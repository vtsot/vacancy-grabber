<?php

declare(strict_types=1);

namespace App\MessageHandler\Command\Source;

use App\Contract\MessageHandler\CommandHandlerInterface;
use App\Entity\Company;
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

    public function __invoke(GrabSourceCommand $command): void
    {
        if (($source = $this->sourceRepository->findOneById($command->sourceId)) === null) {
            throw new EntityNotFoundException(
                $this->translator->trans(id: 'SOURCE_ENTITY.MESSAGE.NOT_FOUND', domain: 'admin-messages')
            );
        }

        $grabber   = $this->grabberFactory->create($source);
        $vacancies = $grabber->grab($source->getUrl());

        foreach ($vacancies as $vacancyDTO) {
            // handle $company
            if (($company = $this->companyRepository->findOneByName($vacancyDTO->company->name)) === null) {
                $company = new Company()
                    ->setName($vacancyDTO->company->name);
            }
            $company
                ->setName($vacancyDTO->company->name)
                ->setUrl($vacancyDTO->company->url);

            // handle $vacancy
            if (($vacancy = $this->vacancyRepository->findOneBySourceIdAndExternalId(
                $source->getId(),
                $vacancyDTO->externalId
            )) === null) {
                $vacancy = new Vacancy()
                    ->setExternalId($vacancyDTO->externalId);
            }

            $vacancy
                ->setCompany($company)
                ->setSource($source)
                ->setTitle($vacancyDTO->title)
                ->setDescription($vacancyDTO->description)
                ->setExternalId($vacancyDTO->externalId)
                ->setUrl($vacancyDTO->url)
                ->setSalary($vacancyDTO->salary);

            $this->em->persist($company);
            $this->em->persist($vacancy);
            $this->em->flush();
        }
    }
}
