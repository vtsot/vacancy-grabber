<?php

declare(strict_types=1);

namespace App\Application\Dashboard\MessageHandler\Query;

use App\Application\Dashboard\DTO\DashboardCollectionDTO;
use App\Application\Dashboard\Message\Query\GetDashboardCollectionQuery;
use App\Application\Dashboard\Message\Query\GetSentResumeToAllResumeZoomableQuery;
use App\Application\Dashboard\Message\Query\GetVacancyStatusDashboardQuery;
use App\Application\Shared\Bus\QueryHandlerInterface;
use App\Infrastructure\Messenger\MessageBusHandler;

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
