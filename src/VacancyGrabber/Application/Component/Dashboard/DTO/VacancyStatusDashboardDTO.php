<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Dashboard\DTO;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class VacancyStatusDashboardDTO
{
    public function __construct(
        #[Groups([
            'dashboard',
        ])]
        #[SerializedName('total_pending')]
        public ?int $totalPending,

        #[Groups([
            'dashboard',
        ])]
        #[SerializedName('total_in_progress')]
        public ?int $totalInProgress,

        #[Groups([
            'dashboard',
        ])]
        #[SerializedName('total_done')]
        public ?int $totalDone,
    ) {
    }
}
