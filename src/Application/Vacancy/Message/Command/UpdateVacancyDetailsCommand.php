<?php

declare(strict_types=1);

namespace App\Application\Vacancy\Message\Command;

use App\Application\Shared\Validation\PatchPropertiesInterface;
use App\Application\Vacancy\MessageHandler\Command\UpdateVacancyDetailsHandler;
use Symfony\Component\Uid\Uuid;

/** @see UpdateVacancyDetailsHandler */
final readonly class UpdateVacancyDetailsCommand implements PatchPropertiesInterface
{
    public function __construct(
        public ?Uuid $userId,
        public Uuid $vacancyId,
        public array $properties,
        public array $fields,
        public array $required = [],
    ) {
    }
}
