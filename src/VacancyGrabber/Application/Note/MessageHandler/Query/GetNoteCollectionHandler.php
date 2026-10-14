<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Note\MessageHandler\Query;

use App\VacancyGrabber\Application\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Application\Note\DTO\NoteCollectionDTO;
use App\VacancyGrabber\Application\Note\DTOFactory\NoteInfoDTOFactory;
use App\VacancyGrabber\Application\Note\Message\Query\GetNoteCollectionQuery;
use App\VacancyGrabber\Application\Security\Voter\NoteVoter;
use App\VacancyGrabber\Application\Shared\Bus\QueryHandlerInterface;
use App\VacancyGrabber\Application\Shared\DTO\CollectionMeta\CollectionFiltersDTO;
use App\VacancyGrabber\Application\Shared\DTO\CollectionMeta\CollectionPaginatorDTO;
use App\VacancyGrabber\Application\Shared\DTO\CollectionMetaDTO;
use App\VacancyGrabber\Application\Shared\Message\Query\Traits\FiltersOrderByTrait;
use App\VacancyGrabber\Domain\Note\Entity\Note;
use App\VacancyGrabber\Domain\Note\Repository\NoteRepository;
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
