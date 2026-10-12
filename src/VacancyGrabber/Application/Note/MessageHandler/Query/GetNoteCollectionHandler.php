<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Note\MessageHandler\Query;

use App\Component\General\DTO\CollectionMeta\CollectionFiltersDTO;
use App\Component\General\DTO\CollectionMeta\CollectionPaginatorDTO;
use App\Component\General\DTO\CollectionMetaDTO;
use App\Component\General\Exception\Security\AccessDeniedException;
use App\Component\General\Message\Query\Traits\FiltersOrderByTrait;
use App\Component\Note\Entity\Note;
use App\Component\Note\Repository\NoteRepository;
use App\Component\Note\Security\Voter\NoteVoter;
use App\Contract\MessageHandler\QueryHandlerInterface;
use App\VacancyGrabber\Application\Note\DTO\NoteCollectionDTO;
use App\VacancyGrabber\Application\Note\DTOFactory\NoteInfoDTOFactory;
use App\VacancyGrabber\Application\Note\Message\Query\GetNoteCollectionQuery;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_key_exists;
use function array_keys;

final readonly class GetNoteCollectionHandler implements QueryHandlerInterface
{
    use FiltersOrderByTrait;

    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private NoteRepository $noteRepository,
        private NoteInfoDTOFactory $noteInfoDTOFactory,
    ) {
    }

    public function __invoke(GetNoteCollectionQuery $query): NoteCollectionDTO
    {
        if (!$this->security->isGranted(NoteVoter::LIST, [$query->userId, Note::class])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $orderBy = array_key_exists($query->orderBy, $query::SORT_BY)
            ? $query::SORT_BY[$query->orderBy]
            : array_values($query::SORT_BY)[0];

        $filterQuery = trim($query->query ?: '') ?: null;

        $page = $this->noteRepository->getAllPaginated(
            userId: $query->userId,
            page: $query->page,
            perPage: $query->perPage,
            query: $filterQuery,
            orderBy: $orderBy,
            orderDirection: $query->orderDirection,
            companyId: $query->company,
        );

        $items = [];

        foreach ($page->getItems() as $item) {
            $items[] = $this->noteInfoDTOFactory->fromEntity($item);
        }

        $availableSortBy  = array_keys($query::SORT_BY);
        $availableFilters = $query::FILTERS;
        $appliedFilters   = [
            $query::FILTER_QUERY => $query->query,
        ];

        return new NoteCollectionDTO(
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
