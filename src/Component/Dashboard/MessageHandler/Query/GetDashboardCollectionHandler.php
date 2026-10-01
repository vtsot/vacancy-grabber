<?php

declare(strict_types=1);

namespace App\Component\Dashboard\MessageHandler\Query;

use App\Component\Dashboard\DTO\DashboardCollectionDTO;
use App\Component\Dashboard\Message\Query\GetDashboardCollectionQuery;
use App\Component\Dashboard\Message\Query\GetSentResumeToAllResumeZoomableQuery;
use App\Component\Dashboard\Message\Query\GetVacancyStatusDashboardQuery;
use App\Contract\MessageHandler\QueryHandlerInterface;
use App\Service\MessageBusHandler;

final readonly class GetDashboardCollectionHandler implements QueryHandlerInterface
{
    public function __construct(
        private MessageBusHandler $messageBusHandler,
    ) {
    }

    public function __invoke(GetDashboardCollectionQuery $query): DashboardCollectionDTO
    {
        $vacancyStatus = $this->messageBusHandler
            ->handleQuery(new GetVacancyStatusDashboardQuery());

        $sentResumeToAllResumeZoomable = $this->messageBusHandler
            ->handleQuery(new GetSentResumeToAllResumeZoomableQuery());

        return new DashboardCollectionDTO(
            vacancyStatus: $vacancyStatus,
            sentResumeToAllResumeZoomable: $sentResumeToAllResumeZoomable
        );
    }
}
