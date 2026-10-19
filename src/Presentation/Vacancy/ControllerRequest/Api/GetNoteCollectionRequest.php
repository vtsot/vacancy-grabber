<?php

declare(strict_types=1);

namespace App\Presentation\Vacancy\ControllerRequest\Api;

use App\Domain\Company\Entity\Company;
use App\Presentation\Shared\ControllerRequest\AbstractGetCollectionRequest;
use App\Presentation\Shared\Validator\Constraint\EntityExists;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class GetNoteCollectionRequest extends AbstractGetCollectionRequest
{
    public function __construct(
        ?int $page = self::DEFAULT_PAGE,
        ?int $perPage = self::DEFAULT_PER_PAGE,
        ?string $query = null,
        ?string $sort = 'noted_at desc',

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

        #[Assert\Date]
        #[Assert\LessThanOrEqual(propertyPath: 'notedAtEndDay')]
        #[OA\Property(format: 'date')]
        #[SerializedName('noted_at_start_day')]
        public ?string $notedAtStartDay = null,

        #[Assert\Date]
        #[Assert\GreaterThanOrEqual(propertyPath: 'notedAtStartDay')]
        #[SerializedName('noted_at_end_day')]
        public ?string $notedAtEndDay = null,

        #[Assert\Uuid]
        #[EntityExists(entity: Company::class)]
        #[OA\Property(type: 'string', format: 'uuid')]
        public ?Uuid $company = null,
    ) {
        parent::__construct($page, $perPage, $query, $sort);
    }
}
