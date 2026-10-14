<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Vacancy\MessageHandler\Query;

use App\Contract\MessageHandler\QueryHandlerInterface;
use App\VacancyGrabber\Application\Component\Company\DTOFactory\CompanySummaryDTOFactory;
use App\VacancyGrabber\Application\Component\Source\DTOFactory\SourceSummaryDTOFactory;
use App\VacancyGrabber\Application\Component\Vacancy\DTO\VacancyCollectionDTO;
use App\VacancyGrabber\Application\Component\Vacancy\DTOFactory\VacancyInfoDTOFactory;
use App\VacancyGrabber\Application\Component\Vacancy\Message\Query\GetVacancyCollectionQuery;
use App\VacancyGrabber\Application\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Application\Security\Voter\VacancyVoter;
use App\VacancyGrabber\Application\Shared\DTO\CollectionMeta\CollectionFiltersDTO;
use App\VacancyGrabber\Application\Shared\DTO\CollectionMeta\CollectionPaginatorDTO;
use App\VacancyGrabber\Application\Shared\DTO\CollectionMetaDTO;
use App\VacancyGrabber\Application\Shared\Message\Query\Traits\FiltersOrderByTrait;
use App\VacancyGrabber\Domain\Company\Repository\CompanyRepository;
use App\VacancyGrabber\Domain\Note\Repository\NoteRepository;
use App\VacancyGrabber\Domain\Source\Repository\SourceRepository;
use App\VacancyGrabber\Domain\Vacancy\Entity\Vacancy;
use App\VacancyGrabber\Domain\Vacancy\Repository\VacancyRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_keys;

final readonly class GetVacancyCollectionHandler implements QueryHandlerInterface
{
    use FiltersOrderByTrait;

    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private VacancyRepository $vacancyRepository,
        private VacancyInfoDTOFactory $vacancyInfoDTOFactory,
        private CompanyRepository $companyRepository,
        private CompanySummaryDTOFactory $companySummaryDTOFactory,
        private NoteRepository $noteRepository,
        private SourceRepository $sourceRepository,
        private SourceSummaryDTOFactory $sourceSummaryDTOFactory,
    ) {
    }

    public function __invoke(GetVacancyCollectionQuery $query): VacancyCollectionDTO
    {
        $criteria = $query->criteria;

        if (!$this->security->isGranted(VacancyVoter::LIST, [$criteria->userId, Vacancy::class])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $page = $this->vacancyRepository->getAllPaginated($criteria);

        $ids = [];

        foreach ($page->getItems() as $item) {
            $ids[] = $item->getId();
        }

        $notesCount = $this->noteRepository->countByVacancyIds(userId: $criteria->userId, vacancyIds: $ids);

        $items = [];

        foreach ($page->getItems() as $item) {
            $items[] = $this->vacancyInfoDTOFactory->fromEntity($item, [
                VacancyInfoDTOFactory::CONTEXT_NOTES_COUNT => $notesCount[$item->getId()->toString()]['total'] ?? 0,
            ]);
        }

        $availableSortBy  = array_keys($query::SORT_BY);
        $availableFilters = $query::FILTERS;
        $appliedFilters   = [
            $query::FILTER_QUERY                 => $criteria->query,
            $query::FILTER_START_DAY             => $criteria->startDay?->format('Y-m-d'),
            $query::FILTER_END_DAY               => $criteria->endDay?->format('Y-m-d'),
            $query::FILTER_SOURCE                => $criteria->source,
            $query::FILTER_COMPANY               => $criteria->company,
            $query::FILTER_STATUS                => $criteria->status?->value,
            $query::FILTER_VIEWED                => $criteria->viewed,
            $query::FILTER_RESUME_SENT_AT        => $criteria->resumeSentAt?->format('Y-m-d'),
            $query::FILTER_RESUME_SENT_START_DAY => $criteria->resumeSentAtStartDay?->format('Y-m-d'),
            $query::FILTER_RESUME_SENT_END_DAY   => $criteria->resumeSentAtEndDay?->format('Y-m-d'),
            $query::FILTER_DEAD_LINE_AT          => $criteria->deadLineAt?->format('Y-m-d'),
            $query::FILTER_DEAD_LINE_START_DAY   => $criteria->deadLineAtStartDay?->format('Y-m-d'),
            $query::FILTER_DEAD_LINE_END_DAY     => $criteria->deadLineAtEndDay?->format('Y-m-d'),
        ];

        return new VacancyCollectionDTO(
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
