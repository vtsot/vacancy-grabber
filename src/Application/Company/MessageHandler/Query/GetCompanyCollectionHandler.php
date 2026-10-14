<?php

declare(strict_types=1);

namespace App\Application\Company\MessageHandler\Query;

use App\Application\Company\DTO\CompanyCollectionDTO;
use App\Application\Company\DTOFactory\CompanyInfoDTOFactory;
use App\Application\Company\Message\Query\GetCompanyCollectionQuery;
use App\Application\Exception\Security\AccessDeniedException;
use App\Application\Security\Voter\CompanyVoter;
use App\Application\Shared\Bus\QueryHandlerInterface;
use App\Application\Shared\DTO\CollectionMeta\CollectionFiltersDTO;
use App\Application\Shared\DTO\CollectionMeta\CollectionPaginatorDTO;
use App\Application\Shared\DTO\CollectionMetaDTO;
use App\Application\Shared\Message\Query\Traits\FiltersOrderByTrait;
use App\Domain\Company\Entity\Company;
use App\Domain\Company\Repository\CompanyRepository;
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
        if (!$this->security->isGranted(CompanyVoter::LIST, [$query->criteria->userId, Company::class])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $orderBy = array_key_exists($query->criteria->orderBy, $query::SORT_BY)
            ? $query::SORT_BY[$query->criteria->orderBy]
            : array_values($query::SORT_BY)[0];

        $page  = $this->companyRepository->getAllPaginated($query->criteria);
        $items = [];

        foreach ($page->getItems() as $item) {
            $items[] = $this->companyInfoDTOFactory->fromEntity($item);
        }

        $availableSortBy  = array_keys($query::SORT_BY);
        $availableFilters = $query::FILTERS;
        $appliedFilters   = [
            $query::FILTER_QUERY => $query->criteria->query,
        ];

        return new CompanyCollectionDTO(
            items: $items,
            meta: new CollectionMetaDTO(
                paginator: new CollectionPaginatorDTO(
                    page: $query->criteria->page,
                    perPage: $query->criteria->perPage,
                    total: $page->getTotalCount(),
                ),
                filters: new CollectionFiltersDTO(
                    query: $query->criteria->query,
                    sort: $this->resolveFiltersOrderBy($orderBy, $query->criteria->orderDirection, $query::SORT_BY),
                    availableSortBy: $availableSortBy,
                    availableFilters: $availableFilters,
                    appliedFilters: $appliedFilters,
                ),
            ),
        );
    }
}
