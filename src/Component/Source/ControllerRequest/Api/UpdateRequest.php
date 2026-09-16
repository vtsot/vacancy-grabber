<?php

declare(strict_types=1);

namespace App\Component\Source\ControllerRequest\Api;

use App\Component\Common\ControllerRequestModifier\TrimModifier;
use App\Contract\ControllerRequest\ControllerRequestInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateRequest implements ControllerRequestInterface
{
    public function __construct(
        #[TrimModifier]
        #[Assert\NotBlank]
        public ?string $name,
        #[Assert\Url]
        public ?string $url,
        #[Assert\Type('bool')]
        public ?bool $active,
    ) {
    }
}
