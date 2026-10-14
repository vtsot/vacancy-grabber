<?php

declare(strict_types=1);

namespace App\Presentation\Note\ControllerRequest\Api;

use App\Domain\Company\Entity\Company;
use App\Presentation\Shared\ControllerRequest\AbstractGetCollectionRequest;
use App\Presentation\Shared\Validator\Constraint\EntityExists;
use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class GetCollectionRequest extends AbstractGetCollectionRequest
{
    public function __construct(
        ?int $page = self::DEFAULT_PAGE,
        ?int $perPage = self::DEFAULT_PER_PAGE,
        ?string $query = null,
        ?string $sort = 'created_at desc',

        #[Assert\Uuid]
        #[EntityExists(entity: Company::class)]
        #[OA\Property(type: 'string', format: 'uuid')]
        public ?Uuid $company = null,
    ) {
        parent::__construct($page, $perPage, $query, $sort);
    }
}
