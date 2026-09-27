<?php

declare(strict_types=1);

namespace App\Component\Source\MessageHandler\Query;

use App\Component\Source\DTO\SourceSummaryDTO;
use App\Component\Source\DTOFactory\SourceSummaryDTOFactory;
use App\Component\Source\Message\Query\GetSourceSummaryQuery;
use App\Component\Source\Repository\SourceRepository;
use App\Contract\MessageHandler\QueryHandlerInterface;

final readonly class GetSourceSummaryHandler implements QueryHandlerInterface
{
    public function __construct(
        private SourceRepository $sourceRepository,
        private SourceSummaryDTOFactory $sourceSummaryDTOFactory,
    ) {
    }

    /**
     * @return array<SourceSummaryDTO>
     */
    public function __invoke(GetSourceSummaryQuery $query): array
    {
        $items = [];

        foreach ($this->sourceRepository->findAllSortByName() as $source) {
            $items[] = $this->sourceSummaryDTOFactory->fromEntity($source);
        }

        return $items;
    }
}
