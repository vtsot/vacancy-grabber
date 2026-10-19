<?php

declare(strict_types=1);

namespace App\Presentation\Note\ControllerRequest\Api;

use App\Presentation\Shared\ControllerRequest\AbstractPatchRequest;
use App\Presentation\Shared\ControllerRequestModifier\StripTagsModifier;
use App\Presentation\Shared\ControllerRequestModifier\TrimModifier;
use App\Presentation\Shared\Util;
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

        #[Assert\Date]
        #[SerializedName('noted_at')]
        public ?string $notedAt = null,

        #[TrimModifier]
        #[StripTagsModifier]
        #[Assert\Length(max: 5000)]
        public ?string $description = null,
    ) {
        parent::__construct(
            properties: $properties,
            required: $required,
            modifiers: array_merge($modifiers, [
                'notedAt' => static fn ($value) => Util::normalizeDate($value),
            ]),
        );
    }
}
