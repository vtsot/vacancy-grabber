<?php

declare(strict_types=1);

namespace App\Component\Source\MessageHandler\Query;

use App\Component\General\DTO\CollectionMeta\CollectionFiltersDTO;
use App\Component\General\DTO\CollectionMeta\CollectionPaginatorDTO;
use App\Component\General\DTO\CollectionMetaDTO;
use App\Component\General\Message\Query\Traits\FiltersOrderByTrait;
use App\Component\Source\DTO\SourceCollectionDTO;
use App\Component\Source\DTOFactory\SourceInfoDTOFactory;
use App\Component\Source\Message\Query\GetSourceCollectionQuery;
use App\Component\Source\Repository\SourceRepository;
use App\Contract\MessageHandler\QueryHandlerInterface;

use function array_key_exists;
use function array_keys;

final readonly class GetSourceCollectionHandler implements QueryHandlerInterface
{
    use FiltersOrderByTrait;

    public function __construct(
        private SourceRepository $sourceRepository,
        private SourceInfoDTOFactory $sourceInfoDTOFactory,
    ) {
    }

    public function __invoke(GetSourceCollectionQuery $query): SourceCollectionDTO
    {
        $orderBy = array_key_exists($query->orderBy, $query::SORT_BY)
            ? $query::SORT_BY[$query->orderBy]
            : array_values($query::SORT_BY)[0];

        $filterQuery = trim($query->query ?: '') ?: null;

        $page = $this->sourceRepository->getAllPaginated(
            $query->page,
            $query->perPage,
            $filterQuery,
            $orderBy,
            $query->orderDirection,
            $query->active,
            $query->startDay,
            $query->endDay,
        );

        $items = [];

        foreach ($page->getItems() as $item) {
            $items[] = $this->sourceInfoDTOFactory->fromEntity($item);
        }

        $availableSortBy  = array_keys($query::SORT_BY);
        $availableFilters = $query::FILTERS;
        $appliedFilters   = [
            $query::FILTER_QUERY     => $query->query,
            $query::FILTER_ACTIVE    => $query->active,
            $query::FILTER_START_DAY => $query->startDay?->format('Y-m-d'),
            $query::FILTER_END_DAY   => $query->endDay?->format('Y-m-d'),
        ];

        return new SourceCollectionDTO(
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
                    availableSortBy: $availableSortBy,
                    availableFilters: $availableFilters,
                    appliedFilters: $appliedFilters,
                ),
            ),
        );
    }
}
