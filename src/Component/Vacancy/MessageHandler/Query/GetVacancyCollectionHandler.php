<?php

declare(strict_types=1);

namespace App\Component\Vacancy\MessageHandler\Query;

use App\Component\Company\DTOFactory\CompanySummaryDTOFactory;
use App\Component\Company\Repository\CompanyRepository;
use App\Component\General\DTO\CollectionMeta\CollectionFiltersDTO;
use App\Component\General\DTO\CollectionMeta\CollectionPaginatorDTO;
use App\Component\General\DTO\CollectionMetaDTO;
use App\Component\General\Message\Query\Traits\FiltersOrderByTrait;
use App\Component\Note\DTOFactory\NoteInfoDTOFactory;
use App\Component\Note\Repository\NoteRepository;
use App\Component\Source\DTOFactory\SourceSummaryDTOFactory;
use App\Component\Source\Repository\SourceRepository;
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
        private NoteRepository $noteRepository,
        private NoteInfoDTOFactory $noteInfoDTOFactory,
        private SourceRepository $sourceRepository,
        private SourceSummaryDTOFactory $sourceSummaryDTOFactory,
    ) {
    }

    public function __invoke(GetVacancyCollectionQuery $query): VacancyCollectionDTO
    {
        $orderBy = array_key_exists($query->orderBy, $query::SORT_BY)
            ? $query::SORT_BY[$query->orderBy]
            : array_values($query::SORT_BY)[0];

        $filterQuery = trim($query->query ?: '') ?: null;

        $page = $this->vacancyRepository->getAllPaginated(
            page: $query->page,
            perPage: $query->perPage,
            query: $filterQuery,
            orderBy: $orderBy,
            orderDirection: $query->orderDirection,
            viewed: $query->viewed,
            startDay: $query->startDay,
            endDay: $query->endDay,
            source: $query->source,
            company: $query->company,
            status: $query->status,
            isResumeSent: $query->isResumeSent,
            resumeSentAt: $query->resumeSentAt,
            resumeSentAtStartDay: $query->resumeSentAtStartDay,
            resumeSentAtEndDay: $query->resumeSentAtStartDay,
        );

        $items = [];

        foreach ($page->getItems() as $item) {
            $notes = [];

            foreach ($this->noteRepository->findAllByVacancyId($item->getId()) as $note) {
                $notes[] = $this->noteInfoDTOFactory->fromEntity($note);
            }

            $items[] = $this->vacancyInfoDTOFactory->fromEntity($item, [
                VacancyInfoDTOFactory::CONTEXT_NOTES => $notes ?? [],
            ]);
        }

        $availableSortBy  = array_keys($query::SORT_BY);
        $availableFilters = $query::FILTERS;
        $appliedFilters   = [
            $query::FILTER_QUERY                 => $query->query,
            $query::FILTER_START_DAY             => $query->startDay?->format('Y-m-d'),
            $query::FILTER_END_DAY               => $query->endDay?->format('Y-m-d'),
            $query::FILTER_SOURCE                => $query->source,
            $query::FILTER_COMPANY               => $query->company,
            $query::FILTER_STATUS                => $query->status?->value,
            $query::FILTER_VIEWED                => $query->viewed,
            $query::FILTER_RESUME_SENT_AT        => $query->resumeSentAt?->format('Y-m-d'),
            $query::FILTER_RESUME_SENT_START_DAY => $query->resumeSentAtStartDay?->format('Y-m-d'),
            $query::FILTER_RESUME_SENT_END_DAY   => $query->resumeSentAtEndDay?->format('Y-m-d'),
        ];

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
                    availableSortBy: $availableSortBy,
                    availableFilters: $availableFilters,
                    appliedFilters: $appliedFilters,
                ),
            ),
        );
    }
}
