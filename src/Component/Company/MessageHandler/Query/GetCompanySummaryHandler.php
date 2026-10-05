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
use App\Contract\MessageHandler\QueryHandlerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class GetCompanySummaryHandler implements QueryHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private CompanyRepository $companyRepository,
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

        $items = [];

        foreach ($this->companyRepository->findAllForUserId(userId: $query->userId) as $source) {
            $items[] = $this->companySummaryDTOFactory->fromEntity($source);
        }

        return $items;
    }
}
