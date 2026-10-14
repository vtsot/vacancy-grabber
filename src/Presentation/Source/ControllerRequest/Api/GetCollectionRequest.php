<?php

declare(strict_types=1);

namespace App\Presentation\Source\ControllerRequest\Api;

use App\Presentation\Shared\ControllerRequest\AbstractGetCollectionRequest;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class GetCollectionRequest extends AbstractGetCollectionRequest
{
    public function __construct(
        ?int $page = self::DEFAULT_PAGE,
        ?int $perPage = self::DEFAULT_PER_PAGE,
        ?string $query = null,
        ?string $sort = null,
        #[Assert\Choice(choices: [0, 1, '0', '1'], strict: true)]
        public mixed $active = null,

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
    ) {
        parent::__construct($page, $perPage, $query, $sort);
    }
}
