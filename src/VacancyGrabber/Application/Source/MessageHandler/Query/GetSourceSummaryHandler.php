<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Source\MessageHandler\Query;

use App\Contract\MessageHandler\QueryHandlerInterface;
use App\VacancyGrabber\Application\Shared\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Application\Source\DTO\SourceSummaryDTO;
use App\VacancyGrabber\Application\Source\DTOFactory\SourceSummaryDTOFactory;
use App\VacancyGrabber\Application\Source\Message\Query\GetSourceSummaryQuery;
use App\VacancyGrabber\Application\Source\Security\Voter\SourceVoter;
use App\VacancyGrabber\Domain\Source\Entity\Source;
use App\VacancyGrabber\Domain\Source\Repository\SourceRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class GetSourceSummaryHandler implements QueryHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private SourceRepositoryInterface $sourceRepository,
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
