<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Source\MessageHandler\Query;

use App\Contract\MessageHandler\QueryHandlerInterface;
use App\VacancyGrabber\Application\Component\Source\DTO\SourceCollectionDTO;
use App\VacancyGrabber\Application\Component\Source\DTOFactory\SourceInfoDTOFactory;
use App\VacancyGrabber\Application\Component\Source\Message\Query\GetSourceCollectionQuery;
use App\VacancyGrabber\Application\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Application\Security\Voter\SourceVoter;
use App\VacancyGrabber\Application\Shared\DTO\CollectionMeta\CollectionFiltersDTO;
use App\VacancyGrabber\Application\Shared\DTO\CollectionMeta\CollectionPaginatorDTO;
use App\VacancyGrabber\Application\Shared\DTO\CollectionMetaDTO;
use App\VacancyGrabber\Application\Shared\Message\Query\Traits\FiltersOrderByTrait;
use App\VacancyGrabber\Domain\Source\Entity\Source;
use App\VacancyGrabber\Domain\Source\Repository\SourceRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_keys;

final readonly class GetSourceCollectionHandler implements QueryHandlerInterface
{
    use FiltersOrderByTrait;

    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private SourceRepository $sourceRepository,
        private SourceInfoDTOFactory $sourceInfoDTOFactory,
    ) {
    }

    public function __invoke(GetSourceCollectionQuery $query): SourceCollectionDTO
    {
        $criteria = $query->criteria;

        if (!$this->security->isGranted(SourceVoter::LIST, [$criteria->userId, Source::class])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $page  = $this->sourceRepository->getAllPaginated($criteria);
        $items = [];

        foreach ($page->getItems() as $item) {
            $items[] = $this->sourceInfoDTOFactory->fromEntity($item);
        }

        $availableSortBy  = array_keys($query::SORT_BY);
        $availableFilters = $query::FILTERS;
        $appliedFilters   = [
            $query::FILTER_QUERY     => $criteria->query,
            $query::FILTER_ACTIVE    => $criteria->active,
            $query::FILTER_START_DAY => $criteria->startDay?->format('Y-m-d'),
            $query::FILTER_END_DAY   => $criteria->endDay?->format('Y-m-d'),
        ];

        return new SourceCollectionDTO(
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
