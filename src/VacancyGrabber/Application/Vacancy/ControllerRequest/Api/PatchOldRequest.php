<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\ControllerRequest\Api;

use App\Component\General\ControllerRequestModifier\StripTagsModifier;
use App\Component\General\ControllerRequestModifier\TrimModifier;
use App\Component\General\Validator\Constraint\AtLeastOneOfPropertyNotNull;
use App\Component\General\Validator\Constraint\EnumValues;
use App\Contract\ControllerRequest\ControllerRequestInterface;
use App\VacancyGrabber\Domain\Vacancy\Entity\VacancyStatus;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[AtLeastOneOfPropertyNotNull(properties: ['title', 'description', 'externalId', 'url', 'salary', 'viewed', 'resumeSentAt', 'deadLineAt', 'status'])]
final readonly class PatchOldRequest implements ControllerRequestInterface
{
    public function __construct(
        #[TrimModifier]
        #[StripTagsModifier]
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
        public ?string $url = null,

        #[TrimModifier]
        #[StripTagsModifier]
        #[Assert\Length(max: 255)]
        public ?string $salary = null,

        #[Assert\Type('bool')]
        public ?bool $viewed = null,

        #[Assert\Date]
        #[SerializedName('resume_sent_at')]
        public ?string $resumeSentAt = null,

        #[Assert\Date]
        #[SerializedName('dead_line_at')]
        public ?string $deadLineAt = null,

        #[EnumValues(VacancyStatus::class)]
        public ?bool $status = null,
    ) {
    }
}
