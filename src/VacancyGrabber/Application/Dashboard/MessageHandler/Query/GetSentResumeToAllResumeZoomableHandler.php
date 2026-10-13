<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Dashboard\MessageHandler\Query;

use App\Contract\MessageHandler\QueryHandlerInterface;
use App\VacancyGrabber\Application\Dashboard\DTO\SentResumeToAllResumeZoomableCollectionDTO;
use App\VacancyGrabber\Application\Dashboard\DTO\SentResumeToAllResumeZoomableInfoDTO;
use App\VacancyGrabber\Application\Dashboard\Message\Query\GetSentResumeToAllResumeZoomableQuery;
use App\VacancyGrabber\Application\Shared\Exception\Security\AccessDeniedException;
use App\VacancyGrabber\Application\Vacancy\Security\Voter\VacancyVoter;
use App\VacancyGrabber\Domain\Vacancy\Entity\Vacancy;
use App\VacancyGrabber\Domain\Vacancy\Repository\VacancyRepository;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriodImmutable;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class GetSentResumeToAllResumeZoomableHandler implements QueryHandlerInterface
{
    public function __construct(
        private Security $security,
        private TranslatorInterface $translator,
        private VacancyRepository $vacancyRepository,
    ) {
    }

    public function __invoke(GetSentResumeToAllResumeZoomableQuery $query): SentResumeToAllResumeZoomableCollectionDTO
    {
        if (!$this->security->isGranted(VacancyVoter::LIST, [$query->userId, Vacancy::class])) {
            throw new AccessDeniedException($this->translator->trans(id: 'SECURITY.ACCESS_DENIED', domain: 'security'));
        }

        if (!$vacancy1st = $this->vacancyRepository->findOneBy(['user' => $query->userId], ['createdAt' => 'ASC'])) {
            return new SentResumeToAllResumeZoomableCollectionDTO(items: []);
        }

        $items               = [];
        $today               = CarbonImmutable::today();
        $period              = CarbonPeriodImmutable::create($vacancy1st->getCreatedAt(), $today);
        $countByCreatedAt    = $this->vacancyRepository->countByCreatedAt($query->userId);
        $countByResumeSentAt = $this->vacancyRepository->countByResumeSentAt($query->userId);
        $countByDeadLineAt   = $this->vacancyRepository->countByDeadLineAt($query->userId);

        foreach ($period as $date) {
            $totalVacancyCreatedAt = $countByCreatedAt[$date->format('Y-m-d')]['total'] ?? 0;
            $totalResumeSentAt     = $countByResumeSentAt[$date->format('Y-m-d')]['total'] ?? 0;
            $totalDeadLineAt       = $countByDeadLineAt[$date->format('Y-m-d')]['total'] ?? 0;

            if ($totalVacancyCreatedAt === 0 && $totalResumeSentAt === 0) {
                continue;
            }

            $items[] = new SentResumeToAllResumeZoomableInfoDTO(
                date: $date,
                vacancies: $totalVacancyCreatedAt,
                resumeSent: $totalResumeSentAt,
                deadLine: $totalDeadLineAt,
            );
        }

        return new SentResumeToAllResumeZoomableCollectionDTO(items: $items);
    }
}
