<?php

declare(strict_types=1);

namespace App\Component\Company\MessageHandler\Query;

use App\Component\Company\DTO\CompanySummaryDTO;
use App\Component\Company\DTOFactory\CompanySummaryDTOFactory;
use App\Component\Company\Message\Query\GetCompanySummaryQuery;
use App\Component\Company\Repository\CompanyRepository;
use App\Contract\MessageHandler\QueryHandlerInterface;

final readonly class GetCompanySummaryHandler implements QueryHandlerInterface
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private CompanySummaryDTOFactory $companySummaryDTOFactory,
    ) {
    }

    /**
     * @return array<CompanySummaryDTO>
     */
    public function __invoke(GetCompanySummaryQuery $query): array
    {
        $items = [];

        foreach ($this->companyRepository->findAllSortByName() as $source) {
            $items[] = $this->companySummaryDTOFactory->fromEntity($source);
        }

        return $items;
    }
}
