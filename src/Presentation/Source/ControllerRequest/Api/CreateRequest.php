<?php

declare(strict_types=1);

namespace App\Presentation\Source\ControllerRequest\Api;

use App\Presentation\Shared\ControllerRequest\ControllerRequestInterface;
use App\Presentation\Shared\ControllerRequestModifier\StripTagsModifier;
use App\Presentation\Shared\ControllerRequestModifier\TrimModifier;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateRequest implements ControllerRequestInterface
{
    public function __construct(
        #[TrimModifier]
        #[StripTagsModifier]
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public ?string $name,

        #[TrimModifier]
        #[StripTagsModifier]
        #[Assert\NotBlank]
        #[Assert\Url]
        #[Assert\Length(max: 255)]
        public ?string $url,

        #[Assert\Type('bool')]
        public ?bool $active,
    ) {
    }
}
