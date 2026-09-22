<?php

declare(strict_types=1);

namespace App\Component\Vacancy\ControllerRequest\Api;

use App\Component\General\ControllerRequestModifier\StripTagsModifier;
use App\Component\General\ControllerRequestModifier\TrimModifier;
use App\Contract\ControllerRequest\ControllerRequestInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateRequest implements ControllerRequestInterface
{
    public function __construct(
        #[TrimModifier]
        #[StripTagsModifier]
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
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
