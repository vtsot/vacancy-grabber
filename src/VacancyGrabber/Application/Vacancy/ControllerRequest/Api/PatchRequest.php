<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\ControllerRequest\Api;

use App\VacancyGrabber\Application\Shared\ControllerRequest\AbstractPatchRequest;
use App\VacancyGrabber\Application\Shared\ControllerRequestModifier\StripTagsModifier;
use App\VacancyGrabber\Application\Shared\ControllerRequestModifier\TrimModifier;
use App\VacancyGrabber\Application\Shared\Service\Util;
use App\VacancyGrabber\Domain\Vacancy\Entity\VacancyStatus;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

use function array_merge;

readonly class PatchRequest extends AbstractPatchRequest
{
    public function __construct(
        // general
        ?array $properties = [],
        ?array $required = [],
        ?array $modifiers = [],

        // extents
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

        #[Assert\Choice(callback: [VacancyStatus::class, 'values'])]
        public ?string $status = null,
    ) {
        parent::__construct(
            properties: $properties,
            required: $required,
            modifiers: array_merge($modifiers, [
                'viewed'       => static fn ($value) => Util::normalizeBool($value),
                'resumeSentAt' => static fn ($value) => Util::normalizeDate($value),
                'deadLineAt'   => static fn ($value) => Util::normalizeDate($value),
                'status'       => static fn ($value) => $value ? VacancyStatus::tryFrom($value) : null,
            ]),
        );
    }
}
