<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Component\Source\ControllerRequest\Api;

use App\Contract\ControllerRequest\ControllerRequestInterface;
use App\VacancyGrabber\Application\ControllerRequestModifier\StripTagsModifier;
use App\VacancyGrabber\Application\ControllerRequestModifier\TrimModifier;
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
        public ?string $name = null,

        #[TrimModifier]
        #[StripTagsModifier]
        #[Assert\Url]
        #[Assert\Length(max: 255)]
        public ?string $url,

        #[Assert\Type('bool')]
        public ?bool $active = null,
    ) {
    }
}
