<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Dashboard\MessageHandler\Query;

use App\Contract\MessageHandler\QueryHandlerInterface;
use App\VacancyGrabber\Application\Dashboard\DTO\VacancyStatusDashboardDTO;
use App\VacancyGrabber\Application\Dashboard\Message\Query\GetVacancyStatusDashboardQuery;
use App\VacancyGrabber\Application\Shared\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Application\Vacancy\Security\Voter\VacancyVoter;
use App\VacancyGrabber\Domain\Vacancy\Entity\Vacancy;
use App\VacancyGrabber\Domain\Vacancy\Entity\VacancyStatus;
use App\VacancyGrabber\Domain\Vacancy\Repository\VacancyRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class GetVacancyStatusDashboardHandler implements QueryHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private VacancyRepositoryInterface $vacancyRepository,
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
