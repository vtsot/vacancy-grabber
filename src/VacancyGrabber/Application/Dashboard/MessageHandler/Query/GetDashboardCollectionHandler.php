<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Dashboard\MessageHandler\Query;

use App\VacancyGrabber\Application\Dashboard\DTO\DashboardCollectionDTO;
use App\VacancyGrabber\Application\Dashboard\Message\Query\GetDashboardCollectionQuery;
use App\VacancyGrabber\Application\Dashboard\Message\Query\GetSentResumeToAllResumeZoomableQuery;
use App\VacancyGrabber\Application\Dashboard\Message\Query\GetVacancyStatusDashboardQuery;
use App\VacancyGrabber\Application\Shared\Bus\QueryHandlerInterface;
use App\VacancyGrabber\Infrastructure\Messenger\MessageBusHandler;

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
