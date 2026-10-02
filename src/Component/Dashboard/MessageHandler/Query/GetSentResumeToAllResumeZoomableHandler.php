<?php

declare(strict_types=1);

namespace App\Component\Dashboard\MessageHandler\Query;

use App\Component\Dashboard\DTO\SentResumeToAllResumeZoomableCollectionDTO;
use App\Component\Dashboard\DTO\SentResumeToAllResumeZoomableInfoDTO;
use App\Component\Dashboard\Message\Query\GetSentResumeToAllResumeZoomableQuery;
use App\Component\General\Exception\Security\AccessDeniedException;
use App\Component\Vacancy\Entity\Vacancy;
use App\Component\Vacancy\Repository\VacancyRepository;
use App\Component\Vacancy\Security\Voter\VacancyVoter;
use App\Contract\MessageHandler\QueryHandlerInterface;
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

        foreach ($period as $date) {
            $totalVacancyCreatedAt = $countByCreatedAt[$date->format('Y-m-d')]['total'] ?? 0;
            $totalResumeSentAt     = $countByResumeSentAt[$date->format('Y-m-d')]['total'] ?? 0;

            if ($totalVacancyCreatedAt === 0 && $totalResumeSentAt === 0) {
                continue;
            }

            $items[] = new SentResumeToAllResumeZoomableInfoDTO(
                date: $date,
                vacancies: $totalVacancyCreatedAt,
                resumeSent: $totalResumeSentAt,
            );
        }

        return new SentResumeToAllResumeZoomableCollectionDTO(items: $items);
    }
}
