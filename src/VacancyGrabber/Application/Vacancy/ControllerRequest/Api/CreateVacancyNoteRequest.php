<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Application\Vacancy\ControllerRequest\Api;

use App\Contract\ControllerRequest\ControllerRequestInterface;
use App\VacancyGrabber\Application\Shared\ControllerRequestModifier\StripTagsModifier;
use App\VacancyGrabber\Application\Shared\ControllerRequestModifier\TrimModifier;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

readonly class CreateVacancyNoteRequest implements ControllerRequestInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Date]
        #[SerializedName('noted_at')]
        public ?string $notedAt = null,

        #[TrimModifier]
        #[StripTagsModifier]
        #[Assert\NotBlank]
        #[Assert\Length(max: 5000)]
        public ?string $description = null,
    ) {
    }
}
