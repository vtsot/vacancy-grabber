<?php

declare(strict_types=1);

namespace App\Presentation\Vacancy\ControllerRequest\Api;

use App\Presentation\Shared\ControllerRequestModifier\StripTagsModifier;
use App\Presentation\Shared\ControllerRequestModifier\TrimModifier;
use App\Presentation\Shared\Util;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateDetailsRequest extends PatchRequest
{
    public function __construct(
        // general
        ?array $properties = [],
        ?array $required = [],
        ?array $modifiers = [],

        // extents
        ?string $title = null,
        ?string $description = null,
        ?string $externalId = null,
        ?string $url = null,
        ?string $salary = null,
        ?bool $viewed = null,
        ?string $resumeSentAt = null,
        ?string $deadLineAt = null,
        ?string $status = null,

        // extents
        #[Assert\Date]
        #[Assert\When(expression: 'this.note != null', constraints: [new Assert\NotBlank()])]
        #[SerializedName('noted_at')]
        public ?string $notedAt = null,

        #[TrimModifier]
        #[StripTagsModifier]
        #[Assert\Length(max: 5000)]
        #[Assert\When(expression: 'this.notedAt != null', constraints: [new Assert\NotBlank()])]
        #[SerializedName('note')]
        public ?string $note = null,
    ) {
        parent::__construct(
            properties: $properties,
            required: $required,
            modifiers: array_merge($modifiers, [
                'notedAt' => static fn ($value) => $value ? Util::normalizeDate($value) : null,
            ]),
            title: $title,
            description: $description,
            externalId: $externalId,
            url: $url,
            salary: $salary,
            viewed: $viewed,
            resumeSentAt: $resumeSentAt,
            deadLineAt: $deadLineAt,
            status: $status
        );
    }
}
