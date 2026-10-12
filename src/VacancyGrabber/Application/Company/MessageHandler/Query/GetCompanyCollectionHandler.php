<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Company\MessageHandler\Query;

use App\Component\General\DTO\CollectionMeta\CollectionFiltersDTO;
use App\Component\General\DTO\CollectionMeta\CollectionPaginatorDTO;
use App\Component\General\DTO\CollectionMetaDTO;
use App\Component\General\Exception\Security\AccessDeniedException;
use App\Component\General\Message\Query\Traits\FiltersOrderByTrait;
use App\Contract\MessageHandler\QueryHandlerInterface;
use App\VacancyGrabber\Application\Company\DTO\CompanyCollectionDTO;
use App\VacancyGrabber\Application\Company\DTOFactory\CompanyInfoDTOFactory;
use App\VacancyGrabber\Application\Company\Message\Query\GetCompanyCollectionQuery;
use App\VacancyGrabber\Application\Company\Security\Voter\CompanyVoter;
use App\VacancyGrabber\Domain\Company\Entity\Company;
use App\VacancyGrabber\Domain\Company\Repository\CompanyRepositoryInterface;
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
        private CompanyRepositoryInterface $companyRepository,
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
