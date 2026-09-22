<?php

declare(strict_types=1);

namespace App\Component\Vacancy\MessageHandler\Query;

use App\Component\Company\DTOFactory\CompanySummaryDTOFactory;
use App\Component\Company\Repository\CompanyRepository;
use App\Component\General\DTO\CollectionMeta\CollectionFiltersDTO;
use App\Component\General\DTO\CollectionMeta\CollectionPaginatorDTO;
use App\Component\General\DTO\CollectionMetaDTO;
use App\Component\General\Message\Query\Traits\FiltersOrderByTrait;
use App\Component\Vacancy\DTO\VacancyCollectionDTO;
use App\Component\Vacancy\DTOFactory\VacancyInfoDTOFactory;
use App\Component\Vacancy\Message\Query\GetVacancyCollectionQuery;
use App\Component\Vacancy\Repository\VacancyRepository;
use App\Contract\MessageHandler\QueryHandlerInterface;

use function array_key_exists;
use function array_keys;

final readonly class GetVacancyCollectionHandler implements QueryHandlerInterface
{
    use FiltersOrderByTrait;

    public function __construct(
        private VacancyRepository $vacancyRepository,
        private VacancyInfoDTOFactory $vacancyInfoDTOFactory,
        private CompanyRepository $companyRepository,
        private CompanySummaryDTOFactory $companySummaryDTOFactory,
    ) {
    }

    public function __invoke(GetVacancyCollectionQuery $query): VacancyCollectionDTO
    {
        $orderBy = array_key_exists($query->orderBy, $query::SORT_BY)
            ? $query::SORT_BY[$query->orderBy]
            : array_values($query::SORT_BY)[0];

        $filterQuery = trim($query->query ?: '') ?: null;

        $page = $this->vacancyRepository->getAllPaginated(
            $query->page,
            $query->perPage,
            $filterQuery,
            $orderBy,
            $query->orderDirection,
            $query->active,
            $query->startDay,
            $query->endDay,
            $query->company,
        );

        $items = [];

        foreach ($page->getItems() as $item) {
            $items[] = $this->vacancyInfoDTOFactory->fromEntity($item);
        }

        return new VacancyCollectionDTO(
            items: $items,
            meta: new CollectionMetaDTO(
                paginator: new CollectionPaginatorDTO(
                    page: $query->page,
                    perPage: $query->perPage,
                    total: $page->getTotalCount(),
                ),
                filters: new CollectionFiltersDTO(
                    query: $filterQuery,
                    sort: $this->resolveFiltersOrderBy($orderBy, $query->orderDirection, $query::SORT_BY),
                    sortBy: array_keys($query::SORT_BY),
                    available: $this->resolveAvailable($query),
                    applied: [
                        $query::FILTER_QUERY     => $query->query,
                        $query::FILTER_ACTIVE    => $query->active,
                        $query::FILTER_START_DAY => $query->startDay?->format('Y-m-d'),
                        $query::FILTER_END_DAY   => $query->endDay?->format('Y-m-d'),
                    ],
                ),
            ),
        );
    }

    private function resolveAvailable(GetVacancyCollectionQuery $query): array
    {
        $companies = [];
        foreach ($this->companyRepository->findAllSortByName() as $company) {
            $companies[] = $this->companySummaryDTOFactory->fromEntity($company);
        }

        $available = $query::FILTERS;
        $available[$query::FILTER_COMPANY] = $companies;

        return $available;
    }
}
