<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Company\MessageHandler\Query;

use App\Contract\MessageHandler\QueryHandlerInterface;
use App\VacancyGrabber\Application\Component\Company\DTO\CompanySummaryDTO;
use App\VacancyGrabber\Application\Component\Company\DTOFactory\CompanySummaryDTOFactory;
use App\VacancyGrabber\Application\Component\Company\Message\Query\GetCompanySummaryQuery;
use App\VacancyGrabber\Application\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Application\Security\Voter\CompanyVoter;
use App\VacancyGrabber\Domain\Company\Entity\Company;
use App\VacancyGrabber\Domain\Company\Repository\CompanyRepository;
use App\VacancyGrabber\Domain\Note\Repository\NoteRepository;
use App\VacancyGrabber\Domain\Vacancy\Repository\VacancyRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class GetCompanySummaryHandler implements QueryHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private CompanyRepository $companyRepository,
        private NoteRepository $noteRepository,
        private VacancyRepository $vacancyRepository,
        private CompanySummaryDTOFactory $companySummaryDTOFactory,
    ) {
    }

    /**
     * @return array<CompanySummaryDTO>
     */
    public function __invoke(GetCompanySummaryQuery $query): array
    {
        if (!$this->security->isGranted(CompanyVoter::LIST, [$query->criteria->userId, Company::class])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $companyIds = match (true) {
            $query->criteria->withNoteOnly === true    => $this->noteRepository->findAllCompanyIdsByUserId($query->criteria->userId),
            $query->criteria->withVacancyOnly === true => $this->vacancyRepository->findAllCompanyIdsByUserId($query->criteria->userId),
            default                                    => [],
        };

        $items     = [];
        $companies = $this->companyRepository->findAllForUserId(userId: $query->criteria->userId, ids: $companyIds);

        foreach ($companies as $company) {
            $items[] = $this->companySummaryDTOFactory->fromEntity($company);
        }

        return $items;
    }
}
