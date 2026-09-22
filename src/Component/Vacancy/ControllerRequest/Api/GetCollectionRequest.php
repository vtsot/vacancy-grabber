<?php

declare(strict_types=1);

namespace App\Component\Vacancy\ControllerRequest\Api;

use App\Component\Company\Entity\Company;
use App\Component\General\ControllerRequest\AbstractGetCollectionRequest;
use App\Component\General\Validator\Constraint\EntityExists;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

use function filter_var;

use const FILTER_VALIDATE_BOOLEAN;

final readonly class GetCollectionRequest extends AbstractGetCollectionRequest
{
    public ?bool $active;

    public function __construct(
        ?int $page = self::DEFAULT_PAGE,
        ?int $perPage = self::DEFAULT_PER_PAGE,
        ?string $query = null,
        ?string $sort = null,
        ?string $active = null,

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

        #[EntityExists(entity: Company::class)]
        public ?string $company = null,
    ) {
        $this->active = $active ? filter_var($active, FILTER_VALIDATE_BOOLEAN) : null;
        parent::__construct($page, $perPage, $query, $sort);
    }
}
