<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Dashboard\MessageHandler\Query;

use App\Contract\MessageHandler\QueryHandlerInterface;
use App\VacancyGrabber\Application\Component\Dashboard\DTO\DashboardCollectionDTO;
use App\VacancyGrabber\Application\Component\Dashboard\Message\Query\GetDashboardCollectionQuery;
use App\VacancyGrabber\Application\Component\Dashboard\Message\Query\GetSentResumeToAllResumeZoomableQuery;
use App\VacancyGrabber\Application\Component\Dashboard\Message\Query\GetVacancyStatusDashboardQuery;
use App\VacancyGrabber\Application\Service\MessageBusHandler;

final readonly class GetDashboardCollectionHandler implements QueryHandlerInterface
{
    public function __construct(
        private MessageBusHandler $messageBusHandler,
    ) {
    }

    public function __invoke(GetDashboardCollectionQuery $query): DashboardCollectionDTO
    {
        $vacancyStatus = $this->messageBusHandler
            ->handleQuery(new GetVacancyStatusDashboardQuery(userId: $query->userId));

        $sentResumeToAllResumeZoomable = $this->messageBusHandler
            ->handleQuery(new GetSentResumeToAllResumeZoomableQuery(userId: $query->userId));

        return new DashboardCollectionDTO(
            vacancyStatus: $vacancyStatus,
            sentResumeToAllResumeZoomable: $sentResumeToAllResumeZoomable
        );
    }
}
