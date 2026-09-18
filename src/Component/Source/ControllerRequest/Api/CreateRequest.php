<?php

declare(strict_types=1);

namespace App\Component\Source\ControllerRequest\Api;

use App\Component\General\ControllerRequestModifier\TrimModifier;
use App\Contract\ControllerRequest\ControllerRequestInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateRequest implements ControllerRequestInterface
{
    public function __construct(
        #[TrimModifier]
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public ?string $name,

        #[TrimModifier]
        #[Assert\Url]
        #[Assert\Length(max: 255)]
        public ?string $url,

        #[Assert\Type('bool')]
        public ?bool $active,
    ) {
    }
}
