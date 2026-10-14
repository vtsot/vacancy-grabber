<?php

declare(strict_types=1);

namespace App\Application\Dashboard\DTO;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class DashboardCollectionDTO
{
    public function __construct(
        #[Groups([
            'dashboard',
        ])]
        #[OA\Property(
            ref: new Model(
                type: VacancyStatusDashboardDTO::class,
                groups: ['dashboard']
            ),
        )]
        #[SerializedName('total_pending')]
        public VacancyStatusDashboardDTO $vacancyStatus,

        #[Groups([
            'dashboard',
        ])]
        #[OA\Property(
            ref: new Model(
                type: SentResumeToAllResumeZoomableCollectionDTO::class,
                groups: ['dashboard']
            ),
        )]
        #[SerializedName('sent_resume_to_all_resume_zoomable')]
        public SentResumeToAllResumeZoomableCollectionDTO $sentResumeToAllResumeZoomable,
    ) {
    }
}
