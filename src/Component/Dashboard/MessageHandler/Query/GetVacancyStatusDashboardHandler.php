<?php

declare(strict_types=1);

namespace App\Component\Dashboard\MessageHandler\Query;

use App\Component\Dashboard\DTO\VacancyStatusDashboardDTO;
use App\Component\Dashboard\Message\Query\GetVacancyStatusDashboardQuery;
use App\Component\General\Exception\Security\AccessDeniedException;
use App\Component\Vacancy\Contract\Dictionary\VacancyStatus;
use App\Component\Vacancy\Entity\Vacancy;
use App\Component\Vacancy\Repository\VacancyRepository;
use App\Component\Vacancy\Security\Voter\VacancyVoter;
use App\Contract\MessageHandler\QueryHandlerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class GetVacancyStatusDashboardHandler implements QueryHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private VacancyRepository $vacancyRepository,
    ) {
    }

    public function __invoke(GetVacancyStatusDashboardQuery $query): VacancyStatusDashboardDTO
    {
        if (!$this->security->isGranted(VacancyVoter::LIST, [$query->userId, Vacancy::class])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        $totals = $this->vacancyRepository->countByStatus($query->userId);

        return new VacancyStatusDashboardDTO(
            totalPending: $totals[VacancyStatus::Pending->value]['total'] ?? 0,
            totalInProgress: $totals[VacancyStatus::InProgress->value]['total'] ?? 0,
            totalDone: $totals[VacancyStatus::Done->value]['total'] ?? 0,
        );
    }
}
