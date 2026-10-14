<?php

declare(strict_types=1);

namespace App\VacancyGrabber\Presentation\Company\ControllerRequest\Api;

use App\VacancyGrabber\Presentation\Shared\ControllerRequest\ControllerRequestInterface;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class GetSummaryRequest implements ControllerRequestInterface
{
    public function __construct(
        #[Assert\Choice(choices: [0, 1, '0', '1', 'false', 'true'], strict: true)]
        #[SerializedName('with_note_only')]
        public mixed $withNoteOnly = null,

        #[Assert\Choice(choices: [0, 1, '0', '1', 'false', 'true'], strict: true)]
        #[SerializedName('with_vacancy_only')]
        public mixed $withVacancyOnly = null,
    ) {
    }
}
