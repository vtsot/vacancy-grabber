<?php

declare(strict_types=1);

namespace App\Component\Company\MessageHandler\Query;

use App\Component\Company\DTO\CompanySummaryDTO;
use App\Component\Company\DTOFactory\CompanySummaryDTOFactory;
use App\Component\Company\Entity\Company;
use App\Component\Company\Message\Query\GetCompanySummaryQuery;
use App\Component\Company\Repository\CompanyRepository;
use App\Component\Company\Security\Voter\CompanyVoter;
use App\Component\General\Exception\Security\AccessDeniedException;
use App\Component\Note\Repository\NoteRepository;
use App\Component\Vacancy\Repository\VacancyRepository;
use App\Contract\MessageHandler\QueryHandlerInterface;
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
        if (!$this->security->isGranted(CompanyVoter::LIST, [$query->userId, Company::class])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $companyIds = match (true) {
            $query->withNoteOnly === true    => $this->noteRepository->findAllCompanyIdsByUserId($query->userId),
            $query->withVacancyOnly === true => $this->vacancyRepository->findAllCompanyIdsByUserId($query->userId),
            default                          => [],
        };

        $items     = [];
        $companies = $this->companyRepository->findAllForUserId(userId: $query->userId, companyIds: $companyIds);

        foreach ($companies as $company) {
            $items[] = $this->companySummaryDTOFactory->fromEntity($company);
        }

        return $items;
    }
}
