<?php

declare(strict_types=1);

namespace App\Component\Vacancy\ControllerRequest\Api;

use App\Component\General\ControllerRequestModifier\StripTagsModifier;
use App\Component\General\ControllerRequestModifier\TrimModifier;
use App\Component\Vacancy\Contract\Dictionary\VacancyStatus;
use App\Contract\ControllerRequest\ControllerRequestInterface;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class PatchRequest implements ControllerRequestInterface
{
    public function __construct(
        // the properties to update
        #[Assert\Count(min: 1)]
        #[Assert\All([
            new Assert\Type(type: 'string'),
            new Assert\NotBlank(),
        ])]
        public ?array $properties = [],
        // then each property validate separately

        #[TrimModifier]
        #[StripTagsModifier]
        #[Assert\Length(max: 255)]
        public ?string $title = null,

        #[TrimModifier]
        #[StripTagsModifier]
        #[Assert\Length(max: 1000)]
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

        #[Assert\Choice(callback: [VacancyStatus::class, 'values'])]
        public ?string $status = null,
    ) {
    }
}
