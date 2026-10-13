<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\MessageHandler\Query;

use App\Component\General\DTO\CollectionMeta\CollectionFiltersDTO;
use App\Component\General\DTO\CollectionMeta\CollectionPaginatorDTO;
use App\Component\General\DTO\CollectionMetaDTO;
use App\Component\General\Message\Query\Traits\FiltersOrderByTrait;
use App\Contract\MessageHandler\QueryHandlerInterface;
use App\VacancyGrabber\Application\Note\DTO\NoteCriteriaDTO;
use App\VacancyGrabber\Application\Vacancy\DTO\VacancyNoteCollectionDTO;
use App\VacancyGrabber\Application\Vacancy\DTOFactory\VacancyNoteDTOFactory;
use App\VacancyGrabber\Application\Vacancy\Message\Query\GetVacancyNoteCollectionQuery;
use App\VacancyGrabber\Domain\Note\Repository\NoteRepositoryInterface;

use function array_key_exists;
use function array_keys;

final readonly class GetVacancyNoteCollectionHandler implements QueryHandlerInterface
{
    use FiltersOrderByTrait;

    public function __construct(
        private NoteRepositoryInterface $noteRepository,
        private VacancyNoteDTOFactory $vacancyNoteDTOFactory,
    ) {
    }

    public function __invoke(GetVacancyNoteCollectionQuery $query): VacancyNoteCollectionDTO
    {
        $orderBy = array_key_exists($query->orderBy, $query::SORT_BY)
            ? $query::SORT_BY[$query->orderBy]
            : array_values($query::SORT_BY)[0];

        $filterQuery = trim($query->query ?: '') ?: null;

        $page = $this->noteRepository->getAllPaginated(new NoteCriteriaDTO(
            userId: $query->userId,
            page: $query->page,
            perPage: $query->perPage,
            query: $filterQuery,
            orderBy: $orderBy,
            orderDirection: $query->orderDirection,
            startDay: $query->startDay,
            endDay: $query->endDay,
            notedAtStartDay: $query->notedAtStartDay,
            notedAtEndDay: $query->notedAtEndDay,
            vacancyId: $query->vacancyId,
        ));

        $items = [];

        foreach ($page->getItems() as $item) {
            $items[] = $this->vacancyNoteDTOFactory->fromEntity($item);
        }

        $availableSortBy  = array_keys($query::SORT_BY);
        $availableFilters = $query::FILTERS;
        $appliedFilters   = [
            $query::FILTER_QUERY     => $query->query,
            $query::FILTER_START_DAY => $query->startDay?->format('Y-m-d'),
            $query::FILTER_END_DAY   => $query->endDay?->format('Y-m-d'),
        ];

        return new VacancyNoteCollectionDTO(
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
