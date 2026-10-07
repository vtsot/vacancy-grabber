<?php

declare(strict_types=1);

namespace App\Component\Vacancy\ControllerRequest\Api;

use App\Component\General\ControllerRequestModifier\StripTagsModifier;
use App\Component\General\ControllerRequestModifier\TrimModifier;
use App\Contract\ControllerRequest\ControllerRequestInterface;
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
