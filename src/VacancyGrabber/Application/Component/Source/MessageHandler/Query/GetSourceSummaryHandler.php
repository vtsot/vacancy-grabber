<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Source\MessageHandler\Query;

use App\Contract\MessageHandler\QueryHandlerInterface;
use App\VacancyGrabber\Application\Component\Source\DTO\SourceSummaryDTO;
use App\VacancyGrabber\Application\Component\Source\DTOFactory\SourceSummaryDTOFactory;
use App\VacancyGrabber\Application\Component\Source\Message\Query\GetSourceSummaryQuery;
use App\VacancyGrabber\Application\Component\Source\Security\Voter\SourceVoter;
use App\VacancyGrabber\Application\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Domain\Source\Entity\Source;
use App\VacancyGrabber\Domain\Source\Repository\SourceRepository;
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
