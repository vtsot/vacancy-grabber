<?php

declare(strict_types=1);

namespace App\Component\Dashboard\MessageHandler\Query;

use App\Component\Dashboard\DTO\SentResumeToAllResumeZoomableCollectionDTO;
use App\Component\Dashboard\DTO\SentResumeToAllResumeZoomableInfoDTO;
use App\Component\Dashboard\Message\Query\GetSentResumeToAllResumeZoomableQuery;
use App\Component\Vacancy\Repository\VacancyRepository;
use App\Contract\MessageHandler\QueryHandlerInterface;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriodImmutable;

final readonly class GetSentResumeToAllResumeZoomableHandler implements QueryHandlerInterface
{
    public function __construct(
        private VacancyRepository $vacancyRepository,
    ) {
    }

    public function __invoke(GetSentResumeToAllResumeZoomableQuery $query): SentResumeToAllResumeZoomableCollectionDTO
    {
        if (!$vacancy1st = $this->vacancyRepository->findOneBy([], ['createdAt' => 'ASC'])) {
            return new SentResumeToAllResumeZoomableCollectionDTO(items: []);
        }

        $items               = [];
        $today               = CarbonImmutable::today();
        $period              = CarbonPeriodImmutable::create($vacancy1st->getCreatedAt(), $today);
        $countByCreatedAt    = $this->vacancyRepository->countByCreatedAt();
        $countByResumeSentAt = $this->vacancyRepository->countByResumeSentAt();

        foreach ($period as $date) {
            $totalVacancyCreatedAt = $countByCreatedAt[$date->format('Y-m-d')]['total'] ?? 0;
            $totalResumeSentAt     = $countByResumeSentAt[$date->format('Y-m-d')]['total'] ?? 0;

            //            if ($totalVacancyCreatedAt === 0 && $totalResumeSentAt === 0) {
            //                continue;
            //            }

            $items[] = new SentResumeToAllResumeZoomableInfoDTO(
                date: $date,
                vacancies: $totalVacancyCreatedAt,
                resumeSent: $totalResumeSentAt,
            );
        }

        return new SentResumeToAllResumeZoomableCollectionDTO(items: $items);
    }
}
