<?php

declare(strict_types=1);

namespace App\Presentation\Vacancy\ControllerRequest\Api;

use App\Domain\Company\Entity\Company;
use App\Domain\Source\Entity\Source;
use App\Domain\Vacancy\Entity\VacancyStatus;
use App\Presentation\Shared\ControllerRequest\AbstractGetCollectionRequest;
use App\Presentation\Shared\Validator\Constraint\EntityExists;
use App\Presentation\Shared\Validator\Constraint\EnumValues;
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

        #[Assert\Choice(choices: [0, 1, '0', '1'], strict: true)]
        public mixed $viewed = null,

        #[Assert\Choice(choices: [0, 1, '0', '1'], strict: true)]
        #[SerializedName('is_resume_sent')]
        public mixed $isResumeSent = null,

        #[Assert\Choice(choices: [0, 1, '0', '1'], strict: true)]
        #[SerializedName('is_dead_line')]
        public mixed $isDeadLine = null,

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

        #[EnumValues(VacancyStatus::class)]
        public ?string $status = null,

        #[Assert\Uuid]
        #[EntityExists(entity: Company::class)]
        #[OA\Property(type: 'string', format: 'uuid')]
        public ?Uuid $company = null,

        #[Assert\Uuid]
        #[EntityExists(entity: Source::class)]
        #[OA\Property(type: 'string', format: 'uuid')]
        public ?Uuid $source = null,

        #[Assert\Date]
        #[OA\Property(format: 'date')]
        #[SerializedName('resume_sent_at')]
        public ?string $resumeSentAt = null,

        #[Assert\Date]
        #[Assert\LessThanOrEqual(propertyPath: 'resumeSentEndDay')]
        #[OA\Property(format: 'date')]
        #[SerializedName('resume_sent_start_day')]
        public ?string $resumeSentStartDay = null,

        #[Assert\Date]
        #[Assert\GreaterThanOrEqual(propertyPath: 'resumeSentStartDay')]
        #[SerializedName('resume_sent_end_day')]
        public ?string $resumeSentEndDay = null,

        #[Assert\Date]
        #[OA\Property(format: 'date')]
        #[SerializedName('dead_line_at')]
        public ?string $deadLineAt = null,

        #[Assert\Date]
        #[Assert\LessThanOrEqual(propertyPath: 'deadLineEndDay')]
        #[OA\Property(format: 'date')]
        #[SerializedName('dead_line_start_day')]
        public ?string $deadLineStartDay = null,

        #[Assert\Date]
        #[Assert\GreaterThanOrEqual(propertyPath: 'deadLineStartDay')]
        #[SerializedName('dead_line_end_day')]
        public ?string $deadLineEndDay = null,
    ) {
        parent::__construct($page, $perPage, $query, $sort);
    }
}
