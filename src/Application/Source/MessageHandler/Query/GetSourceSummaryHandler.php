<?php

declare(strict_types=1);

namespace App\Application\Source\MessageHandler\Query;

use App\Application\Exception\Security\AccessDeniedException;
use App\Application\Security\Voter\SourceVoter;
use App\Application\Shared\Bus\QueryHandlerInterface;
use App\Application\Source\DTO\SourceSummaryDTO;
use App\Application\Source\DTOFactory\SourceSummaryDTOFactory;
use App\Application\Source\Message\Query\GetSourceSummaryQuery;
use App\Domain\Source\Entity\Source;
use App\Domain\Source\Repository\SourceRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class GetSourceSummaryHandler implements QueryHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private SourceRepository $sourceRepository,
        private SourceSummaryDTOFactory $sourceSummaryDTOFactory,
    ) {
    }

    /**
     * @return array<SourceSummaryDTO>
     */
    public function __invoke(GetSourceSummaryQuery $query): array
    {
        if (!$this->security->isGranted(SourceVoter::LIST, [$query->userId, Source::class])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $items = [];

        foreach ($this->sourceRepository->findAllSortByName($query->userId) as $source) {
            $items[] = $this->sourceSummaryDTOFactory->fromEntity($source);
        }

        return $items;
    }
}
