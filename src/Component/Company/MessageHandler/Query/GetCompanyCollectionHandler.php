<?php

declare(strict_types=1);

namespace App\Component\Company\MessageHandler\Query;

use App\Component\Company\DTO\CompanyCollectionDTO;
use App\Component\Company\DTOFactory\CompanyInfoDTOFactory;
use App\Component\Company\Entity\Company;
use App\Component\Company\Message\Query\GetCompanyCollectionQuery;
use App\Component\Company\Repository\CompanyRepository;
use App\Component\Company\Security\Voter\CompanyVoter;
use App\Component\General\DTO\CollectionMeta\CollectionFiltersDTO;
use App\Component\General\DTO\CollectionMeta\CollectionPaginatorDTO;
use App\Component\General\DTO\CollectionMetaDTO;
use App\Component\General\Exception\Security\AccessDeniedException;
use App\Component\General\Message\Query\Traits\FiltersOrderByTrait;
use App\Contract\MessageHandler\QueryHandlerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_key_exists;
use function array_keys;

final readonly class GetCompanyCollectionHandler implements QueryHandlerInterface
{
    use FiltersOrderByTrait;

    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private CompanyRepository $companyRepository,
        private CompanyInfoDTOFactory $companyInfoDTOFactory,
    ) {
    }

    public function __invoke(GetCompanyCollectionQuery $query): CompanyCollectionDTO
    {
        if (!$this->security->isGranted(CompanyVoter::LIST, [$query->userId, Company::class])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $orderBy = array_key_exists($query->orderBy, $query::SORT_BY)
            ? $query::SORT_BY[$query->orderBy]
            : array_values($query::SORT_BY)[0];

        $filterQuery = trim($query->query ?: '') ?: null;

        $page = $this->companyRepository->getAllPaginated(
            userId: $query->userId,
            page: $query->page,
            perPage: $query->perPage,
            query: $filterQuery,
            orderBy: $orderBy,
            orderDirection: $query->orderDirection,
        );

        $items = [];

        foreach ($page->getItems() as $item) {
            $items[] = $this->companyInfoDTOFactory->fromEntity($item);
        }

        $availableSortBy  = array_keys($query::SORT_BY);
        $availableFilters = $query::FILTERS;
        $appliedFilters   = [
            $query::FILTER_QUERY => $query->query,
        ];

        return new CompanyCollectionDTO(
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
