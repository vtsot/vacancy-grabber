<?php

declare(strict_types=1);

namespace App\Application\Vacancy\MessageHandler\Query;

use App\Application\Shared\Bus\QueryHandlerInterface;
use App\Application\Shared\DTO\CollectionMeta\CollectionFiltersDTO;
use App\Application\Shared\DTO\CollectionMeta\CollectionPaginatorDTO;
use App\Application\Shared\DTO\CollectionMetaDTO;
use App\Application\Shared\Message\Query\Traits\FiltersOrderByTrait;
use App\Application\Vacancy\DTO\VacancyNoteCollectionDTO;
use App\Application\Vacancy\DTOFactory\VacancyNoteDTOFactory;
use App\Application\Vacancy\Message\Query\GetVacancyNoteCollectionQuery;
use App\Domain\Note\Repository\NoteRepository;

use function array_keys;

final readonly class GetVacancyNoteCollectionHandler implements QueryHandlerInterface
{
    use FiltersOrderByTrait;

    public function __construct(
        private NoteRepository $noteRepository,
        private VacancyNoteDTOFactory $vacancyNoteDTOFactory,
    ) {
    }

    public function __invoke(GetVacancyNoteCollectionQuery $query): VacancyNoteCollectionDTO
    {
        $criteria = $query->criteria;
        $page     = $this->noteRepository->getAllPaginated($criteria);

        $items = [];

        foreach ($page->getItems() as $item) {
            $items[] = $this->vacancyNoteDTOFactory->fromEntity($item);
        }

        $availableSortBy  = array_keys($query::SORT_BY);
        $availableFilters = $query::FILTERS;
        $appliedFilters   = [
            $query::FILTER_QUERY     => $criteria->query,
            $query::FILTER_START_DAY => $criteria->startDay?->format('Y-m-d'),
            $query::FILTER_END_DAY   => $criteria->endDay?->format('Y-m-d'),
        ];

        return new VacancyNoteCollectionDTO(
            items: $items,
            meta: new CollectionMetaDTO(
                paginator: new CollectionPaginatorDTO(
                    page: $criteria->page,
                    perPage: $criteria->perPage,
                    total: $page->getTotalCount(),
                ),
                filters: new CollectionFiltersDTO(
                    query: $criteria->query,
                    sort: $this->resolveFiltersOrderBy($criteria->orderBy, $criteria->orderDirection, $query::SORT_BY),
                    availableSortBy: $availableSortBy,
                    availableFilters: $availableFilters,
                    appliedFilters: $appliedFilters,
                ),
            ),
        );
    }
}
