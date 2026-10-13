<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Source\ControllerRequest\Api;

use App\Contract\ControllerRequest\ControllerRequestInterface;
use App\VacancyGrabber\Application\Shared\ControllerRequestModifier\StripTagsModifier;
use App\VacancyGrabber\Application\Shared\ControllerRequestModifier\TrimModifier;
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
