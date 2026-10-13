<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\ControllerRequest\Api;

use App\Contract\ControllerRequest\ControllerRequestInterface;
use App\VacancyGrabber\Application\Shared\ControllerRequestModifier\StripTagsModifier;
use App\VacancyGrabber\Application\Shared\ControllerRequestModifier\TrimModifier;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class PatchVacancyNoteRequest implements ControllerRequestInterface
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

        #[Assert\Date]
        #[SerializedName('noted_at')]
        public ?string $notedAt = null,

        #[TrimModifier]
        #[StripTagsModifier]
        #[Assert\Length(max: 5000)]
        public ?string $description = null,
    ) {
    }
}
