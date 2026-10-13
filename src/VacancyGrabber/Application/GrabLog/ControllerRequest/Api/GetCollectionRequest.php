<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\GrabLog\ControllerRequest\Api;

use App\VacancyGrabber\Application\Shared\ControllerRequest\AbstractGetCollectionRequest;
use App\VacancyGrabber\Application\Shared\Validator\Constraint\EntityExists;
use App\VacancyGrabber\Domain\Source\Entity\Source;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class GetCollectionRequest extends AbstractGetCollectionRequest
{
    public function __construct(
        ?int $page = self::DEFAULT_PAGE,
        ?int $perPage = self::DEFAULT_PER_PAGE,
        ?string $query = null,
        ?string $sort = 'created_at desc',

        #[Assert\Date]
        #[Assert\LessThanOrEqual(propertyPath: 'endDay')]
        #[OA\Property(format: 'date')]
        #[SerializedName('start_day')]
        public ?string $startDay = null,

        #[Assert\Date]
        #[Assert\GreaterThanOrEqual(propertyPath: 'startDay')]
        #[OA\Property(format: 'date')]
        #[SerializedName('end_day')]
        public ?string $endDay = null,

        #[Assert\Uuid]
        #[EntityExists(entity: Source::class)]
        #[OA\Property(type: 'string', format: 'uuid')]
        public ?Uuid $source = null,
    ) {
        parent::__construct($page, $perPage, $query, $sort);
    }
}
