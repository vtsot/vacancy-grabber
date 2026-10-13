<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\ControllerRequest\Api;

use App\Component\General\ControllerRequestModifier\StripTagsModifier;
use App\Component\General\ControllerRequestModifier\TrimModifier;
use App\Component\General\Validator\Constraint\EntityExists;
use App\Component\General\Validator\Constraint\EnumValues;
use App\Contract\ControllerRequest\ControllerRequestInterface;
use App\VacancyGrabber\Domain\Company\Entity\Company;
use App\VacancyGrabber\Domain\Source\Entity\Source;
use App\VacancyGrabber\Domain\Vacancy\Entity\VacancyStatus;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

readonly class CreateRequest implements ControllerRequestInterface
{
    public function __construct(
        #[TrimModifier]
        #[StripTagsModifier]
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public ?string $title = null,

        #[TrimModifier]
        #[StripTagsModifier]
        #[Assert\Length(max: 5000)]
        public ?string $description = null,

        #[TrimModifier]
        #[StripTagsModifier]
        #[Assert\Length(max: 255)]
        #[SerializedName('external_id')]
        public ?string $externalId = null,

        #[TrimModifier]
        #[StripTagsModifier]
        #[Assert\Length(max: 255)]
        #[Assert\Url]
        public ?string $url = null,

        #[TrimModifier]
        #[StripTagsModifier]
        #[Assert\Length(max: 255)]
        public ?string $salary = null,

        #[Assert\Sequentially([
            new Assert\Uuid(),
            new EntityExists(entity: Source::class),
        ])]
        #[OA\Property(type: 'string', format: 'uuid')]
        #[SerializedName('source_id')]
        public ?string $sourceId = null,

        #[Assert\Sequentially([
            new Assert\Uuid(),
            new EntityExists(entity: Company::class),
        ])]
        #[OA\Property(type: 'string', format: 'uuid')]
        #[SerializedName('company_id')]
        public ?string $companyId = null,

        #[EnumValues(VacancyStatus::class)]
        public ?string $status = null,

        #[Assert\Type('bool')]
        public ?bool $viewed = null,

        #[Assert\Date]
        #[SerializedName('resume_sent_at')]
        public ?string $resumeSentAt = null,

        #[Assert\Date]
        #[SerializedName('dead_line_at')]
        public ?string $deadLineAt = null,
    ) {
    }
}
