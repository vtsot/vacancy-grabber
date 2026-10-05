<?php

declare(strict_types=1);

namespace App\Component\Source\MessageHandler\Command;

use App\Component\Company\Entity\Company;
use App\Component\Company\Repository\CompanyRepository;
use App\Component\General\Exception\Security\AccessDeniedException;
use App\Component\General\Exception\Validation\EntityNotFoundException;
use App\Component\GrabLog\Entity\GrabLog;
use App\Component\Source\Entity\Source;
use App\Component\Source\Message\Command\GrabSourceCommand;
use App\Component\Source\Repository\SourceRepository;
use App\Component\Source\Security\Voter\SourceVoter;
use App\Component\User\Entity\User;
use App\Component\Vacancy\Entity\Vacancy;
use App\Component\Vacancy\Repository\VacancyRepository;
use App\Component\VacancyGrabber\DTO\CompanyDTO;
use App\Component\VacancyGrabber\DTO\VacancyDTO;
use App\Component\VacancyGrabber\Factory\GrabberFactory;
use App\Contract\MessageHandler\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final class GrabSourceHandler implements CommandHandlerInterface
{
    private int $counterCompanyCreated = 0;
    private int $counterVacancyCreated = 0;

    public function __construct(
        private readonly Security $security,
        private readonly TranslatorInterface $translator,
        private readonly SourceRepository $sourceRepository,
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

    /**
     * @throws ORMException
     */
    private function saveVacancy(GrabSourceCommand $command, Source $source, ?Company $company, VacancyDTO $vacancyDTO): Vacancy
    {
        $vacancy = $this->vacancyRepository
            ->findOneByUserIdAndSourceIdAndExternalId(
                userId: $command->userId,
                sourceId: $source->getId(),
                externalId: $vacancyDTO->externalId
            );

        if ($vacancy === null) {
            $vacancy = new Vacancy()
                ->setUser($command->userId ? $this->em->getReference(User::class, $command->userId) : null)
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
     * @throws ORMException
     */
    public function __invoke(GrabSourceCommand $command): int
    {
        if (!($source = $this->sourceRepository->findOneById($command->sourceId))) {
            throw new EntityNotFoundException(
                $this->translator->trans(id: 'SOURCE.NOT_FOUND', domain: 'validators')
            );
        }

        if (!$this->security->isGranted(SourceVoter::GRAB, [$command->userId, $source])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $grabber   = $this->grabberFactory->create($source);
        $vacancies = $grabber->grab($source->getUrl());

        // handle vacancies
        foreach ($vacancies as $vacancyDTO) {
            $company = $vacancyDTO->company ? $this->saveCompany($vacancyDTO->company) : null;
            $vacancy = $this->saveVacancy($command, $source, $company, $vacancyDTO);
        }

        if ($this->counterVacancyCreated) {
            $gabLog = new GrabLog()
                ->setUser($command->userId ? $this->em->getReference(User::class, $command->userId) : null)
                ->setSource($source)
                ->setCounterCompanyCreated($this->counterCompanyCreated)
                ->setCounterVacancyCreated($this->counterVacancyCreated);

            $this->em->persist($gabLog);
            $this->em->flush();
        }

        return $this->counterVacancyCreated;
    }
}
