<?php

declare(strict_types=1);

namespace App\Component\Dashboard\MessageHandler\Query;

use App\Component\Dashboard\DTO\VacancyStatusDashboardDTO;
use App\Component\Dashboard\Message\Query\GetVacancyStatusDashboardQuery;
use App\Component\Vacancy\Contract\Dictionary\VacancyStatus;
use App\Component\Vacancy\Repository\VacancyRepository;
use App\Contract\MessageHandler\QueryHandlerInterface;

final readonly class GetVacancyStatusDashboardHandler implements QueryHandlerInterface
{
    public function __construct(
        private VacancyRepository $vacancyRepository,
    ) {
    }

    public function __invoke(GetVacancyStatusDashboardQuery $query): VacancyStatusDashboardDTO
    {
        $totals = $this->vacancyRepository->countByStatus();

        return new VacancyStatusDashboardDTO(
            totalPending: $totals[VacancyStatus::Pending->value]['total'] ?? 0,
            totalInProgress: $totals[VacancyStatus::InProgress->value]['total'] ?? 0,
            totalDone: $totals[VacancyStatus::Done->value]['total'] ?? 0,
        );
    }
}
