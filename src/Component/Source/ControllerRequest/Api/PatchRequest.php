<?php

declare(strict_types=1);

namespace App\Component\Source\ControllerRequest\Api;

use App\Component\General\ControllerRequestModifier\StripTagsModifier;
use App\Component\General\ControllerRequestModifier\TrimModifier;
use App\Component\General\Validator\Constraint\AtLeastOneOfPropertyNotNull;
use App\Contract\ControllerRequest\ControllerRequestInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[AtLeastOneOfPropertyNotNull(properties: ['name', 'url', 'active'])]
final readonly class PatchRequest implements ControllerRequestInterface
{
    public function __construct(
        #[TrimModifier]
        #[StripTagsModifier]
        #[Assert\Length(max: 255)]
        #[Assert\NotBlank]
        public ?string $name,

        #[TrimModifier]
        #[StripTagsModifier]
        #[Assert\Url]
        #[Assert\Length(max: 255)]
        public ?string $url,

        #[Assert\Type('bool')]
        public ?bool $active,
    ) {
    }
}
