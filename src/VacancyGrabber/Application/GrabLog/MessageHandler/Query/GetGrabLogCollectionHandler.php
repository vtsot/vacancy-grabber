<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\GrabLog\MessageHandler\Query;

use App\Contract\MessageHandler\QueryHandlerInterface;
use App\VacancyGrabber\Application\GrabLog\DTO\GrabLogCollectionDTO;
use App\VacancyGrabber\Application\GrabLog\DTOFactory\GrabLogInfoDTOFactory;
use App\VacancyGrabber\Application\GrabLog\Message\Query\GetGrabLogCollectionQuery;
use App\VacancyGrabber\Application\GrabLog\Security\Voter\GrabLogVoter;
use App\VacancyGrabber\Application\Shared\DTO\CollectionMeta\CollectionFiltersDTO;
use App\VacancyGrabber\Application\Shared\DTO\CollectionMeta\CollectionPaginatorDTO;
use App\VacancyGrabber\Application\Shared\DTO\CollectionMetaDTO;
use App\VacancyGrabber\Application\Shared\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Application\Shared\Message\Query\Traits\FiltersOrderByTrait;
use App\VacancyGrabber\Domain\GrabLog\Entity\GrabLog;
use App\VacancyGrabber\Domain\GrabLog\Repository\GrabLogRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_keys;

final readonly class GetGrabLogCollectionHandler implements QueryHandlerInterface
{
    use FiltersOrderByTrait;

    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private GrabLogRepository $grabLogRepository,
        private GrabLogInfoDTOFactory $grabLogInfoDTOFactory,
    ) {
    }

    public function __invoke(GetGrabLogCollectionQuery $query): GrabLogCollectionDTO
    {
        $criteria = $query->criteria;

        if (!$this->security->isGranted(GrabLogVoter::LIST, [$criteria->userId, GrabLog::class])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $page  = $this->grabLogRepository->getAllPaginated($criteria);
        $items = [];

        foreach ($page->getItems() as $item) {
            $items[] = $this->grabLogInfoDTOFactory->fromEntity($item);
        }

        $availableSortBy  = array_keys($query::SORT_BY);
        $availableFilters = $query::FILTERS;
        $appliedFilters   = [
            $query::FILTER_QUERY     => $criteria->query,
            $query::FILTER_START_DAY => $criteria->startDay?->format('Y-m-d'),
            $query::FILTER_END_DAY   => $criteria->endDay?->format('Y-m-d'),
        ];

        return new GrabLogCollectionDTO(
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
