<?php

declare(strict_types=1);

namespace App\Component\Vacancy\Message\Command;

use App\Component\Vacancy\MessageHandler\Command\PatchVacancyHandler;
use Symfony\Component\Uid\Uuid;

/** @see PatchVacancyHandler */
final readonly class PatchVacancyCommand
{
    public function __construct(
        public Uuid $id,
        public ?string $name,
        public ?string $url,
        public ?bool $active,
    ) {
    }
}
