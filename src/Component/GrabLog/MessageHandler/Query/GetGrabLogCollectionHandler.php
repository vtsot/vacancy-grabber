<?php

declare(strict_types=1);

namespace App\Component\GrabLog\MessageHandler\Query;

use App\Component\General\DTO\CollectionMeta\CollectionFiltersDTO;
use App\Component\General\DTO\CollectionMeta\CollectionPaginatorDTO;
use App\Component\General\DTO\CollectionMetaDTO;
use App\Component\General\Message\Query\Traits\FiltersOrderByTrait;
use App\Component\GrabLog\DTO\GrabLogCollectionDTO;
use App\Component\GrabLog\DTOFactory\GrabLogInfoDTOFactory;
use App\Component\GrabLog\Message\Query\GetGrabLogCollectionQuery;
use App\Component\GrabLog\Repository\GrabLogRepository;
use App\Contract\MessageHandler\QueryHandlerInterface;

use function array_key_exists;
use function array_keys;

final readonly class GetGrabLogCollectionHandler implements QueryHandlerInterface
{
    use FiltersOrderByTrait;

    public function __construct(
        private GrabLogRepository $grabLogRepository,
        private GrabLogInfoDTOFactory $grabLogInfoDTOFactory,
    ) {
    }

    public function __invoke(GetGrabLogCollectionQuery $query): GrabLogCollectionDTO
    {
        $orderBy = array_key_exists($query->orderBy, $query::SORT_BY)
            ? $query::SORT_BY[$query->orderBy]
            : array_values($query::SORT_BY)[0];

        $filterQuery = trim($query->query ?: '') ?: null;

        $page = $this->grabLogRepository->getAllPaginated(
            page: $query->page,
            perPage: $query->perPage,
            query: $filterQuery,
            orderBy: $orderBy,
            orderDirection: $query->orderDirection,
            startDay: $query->startDay,
            endDay: $query->endDay,
            source: $query->source,
        );

        $items = [];

        foreach ($page->getItems() as $item) {
            $items[] = $this->grabLogInfoDTOFactory->fromEntity($item);
        }

        $availableSortBy  = array_keys($query::SORT_BY);
        $availableFilters = $query::FILTERS;
        $appliedFilters   = [
            $query::FILTER_QUERY     => $query->query,
            $query::FILTER_START_DAY => $query->startDay?->format('Y-m-d'),
            $query::FILTER_END_DAY   => $query->endDay?->format('Y-m-d'),
        ];

        return new GrabLogCollectionDTO(
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
