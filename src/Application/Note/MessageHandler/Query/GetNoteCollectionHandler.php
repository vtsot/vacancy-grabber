<?php

declare(strict_types=1);

namespace App\Application\Note\MessageHandler\Query;

use App\Application\Exception\Security\AccessDeniedException;
use App\Application\Note\DTO\NoteCollectionDTO;
use App\Application\Note\DTOFactory\NoteInfoDTOFactory;
use App\Application\Note\Message\Query\GetNoteCollectionQuery;
use App\Application\Security\Voter\NoteVoter;
use App\Application\Shared\Bus\QueryHandlerInterface;
use App\Application\Shared\DTO\CollectionMeta\CollectionFiltersDTO;
use App\Application\Shared\DTO\CollectionMeta\CollectionPaginatorDTO;
use App\Application\Shared\DTO\CollectionMetaDTO;
use App\Application\Shared\Message\Query\Traits\FiltersOrderByTrait;
use App\Domain\Note\Entity\Note;
use App\Domain\Note\Repository\NoteRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

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
        $criteria = $query->criteria;

        if (!$this->security->isGranted(NoteVoter::LIST, [$criteria->userId, Note::class])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $page  = $this->noteRepository->getAllPaginated($criteria);
        $items = [];

        foreach ($page->getItems() as $item) {
            $items[] = $this->noteInfoDTOFactory->fromEntity($item);
        }

        $availableSortBy  = array_keys($query::SORT_BY);
        $availableFilters = $query::FILTERS;
        $appliedFilters   = [
            $query::FILTER_QUERY => $criteria->query,
        ];

        return new NoteCollectionDTO(
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
